<?php
namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\Payout;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Notifications\InvoicePayoutNotification;
use App\Enums\ReservationStatus;
use Illuminate\Support\Facades\File;


class InvoiceService
{
   public function generateInvoice(User $guide, $baseAmount, $fileService, $payout,$debut,$fin)
{
    
    // Récupérer toutes les réservations du guide sur la période
    $reservations = Reservation::join('guide_experiences', 'reservations.experience_id', '=', 'guide_experiences.id')
        ->select('reservations.*', 'guide_experiences.title as experience_title')
        ->where('guide_experiences.user_id', $guide->id)
        ->whereBetween('reservations.date_time', [$debut, $fin])
        ->where('reservations.guide_payout_amount', '!=', 0)
        ->orderBy('reservations.date_time')
        ->get();

    $type = $guide->isProWithTva() ? "Professionnel assujetti à la TVA" : ( ($guide->isLocal() )? "Particulier non professionnel" : "Professionnel non assujetti à la TVA (franchise en base)" ) ;
    $titre = $guide->isProWithTva() ? "FACTURE D’AUTOFACTURATION" : ( ($guide->isLocal() )? "REÇU D’AUTOFACTURATION" : "FACTURE D’AUTOFACTURATION" ) ;

    $totalHT  = 0;
    $totalTVA = 0;
    $totalTTC = 0;

    foreach ($reservations as $resa) {
        $amount = $resa->guide_payout_amount;

        if ($guide->isProWithTva()) {
            $ht  = $amount / 1.20;   // base hors taxe
            $tva = $amount - $ht;    // TVA
        } else {
            $ht  = $amount;
            $tva = 0;
        }

        $ttc = $ht + $tva;

        // Ajouter les valeurs calculées à l'objet réservation
        $resa->ht   = round($ht, 2);
        $resa->tva  = round($tva, 2);
        $resa->ttc  = round($ttc, 2);

        // Info groupe & participants
        $resa->is_group = $resa->is_group ? "Oui" : "Non";
        $resa->people   = $resa->nombre_des_voyageurs ?? 1;

        if ($resa->guide_payout_percentage == 100)
        {
            if($resa->status === ReservationStatus::ARCHIVÉE->value )
                $resa->status_resa = "Réalisée";
            else
                $resa->status_resa = "Annulée (< 24h) → 100%";
        }
        else if ($resa->guide_payout_percentage == 50)
            $resa->status_resa = "Annulée (24h–72h avant) → 50%";
        else
            $resa->status_resa = "Annulée (> 72h avant) → 0%";


        // Cumuler pour les totaux
        $totalHT  += $ht;
        $totalTVA += $tva;
        $totalTTC += $ttc;
    }
    $data = [
        "guide"        => $guide,
        "title"        => $titre,
        'client_name'  => $guide->name_of_company ?? $guide->name,
        'client_type'  => $type,
        'client_siret' => $guide->siren_number??"",
        'client_phone' => $guide->phone_number??"non reseigné",
        'reservations' => $reservations,
        'total_ht'     => round($totalHT, 2),
        'total_tva'    => round($totalTVA, 2),
        'total_ttc'    => round($totalTTC, 2),
        'invoice_id'   => "MP-{$payout->id}-{$guide->id}-{$payout->payment_period}", 
        'period'       => $payout->payment_period,
        'iban'         => $guide->iban
    ];

    // Uploader sur S3 pour archivage
        $view = $guide->isProWithTva() ? "facture-pro" : ( ($guide->isLocal() )? "facture-local" : "facture-auto-entrep" ) ;



    // Génération du PDF
    $pdf = Pdf::loadView($view, $data);
    $pdfContent = $pdf->output();

    // Nom du fichier
    $fileName = "facture-{$guide->id}-" . $payout->payment_period . ".pdf";

    // (Optionnel) envoi par mail avec PJ
    // Mail::to($guide->email)->send(new GuideInvoiceMail($guide, $pdfContent, $fileName));

   




    $url = $fileService->uploadToS3($pdfContent, "Factures", $fileName);

    $tempDir = storage_path('app/temp');
    File::ensureDirectoryExists($tempDir);
    $tempPath = $tempDir . '/' . $fileName;
    File::put($tempPath, $pdfContent);

    // Appel notif
    $guide->notify(new InvoicePayoutNotification($guide, $tempPath, $fileName, $data['title'],$payout->payment_period));

    unlink($tempPath);

    return $url;
}


}
