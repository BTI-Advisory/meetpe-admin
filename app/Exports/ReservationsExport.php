<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Reservation;
use App\Enums\ReservationStatus;

class ReservationsExport implements FromArray, WithHeadings
{


    public function array(): array
    {
        $rows = [];

        $reservations = Reservation::with([
            'voyageur',
            'experience.user', // guide
        ])->whereNotIn("status",[ReservationStatus::CREATED->value])->latest()->get();

        foreach ($reservations as $reservation) {
            $experience = $reservation->experience;
            $guide = $experience?->user;
            $voyageur = $reservation->voyageur;

            $rows[] = [
                $reservation->date_time,
                $experience?->title,
                $guide?->name,
                $guide?->email,
                $voyageur?->name,
                $voyageur?->email,
                $experience?->title,
                $reservation->status,
                $reservation->nombre_des_voyageurs,
                $reservation->is_group ? 'Groupe' : 'Individuel',
                $reservation->total_price,
                $reservation->guide_payout_amount,
                $reservation->refund_amount > 0 ? 'Oui' : 'Non',
                $reservation->refund_amount ?? '',
                $reservation->status === ReservationStatus::ANNULÉE->value ? 'Oui' : 'Non',
                $reservation->cancel_reason,
                $reservation->cancel_description,
                $reservation->stripe_refund_status ?? '',
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Date de réservation',
            'Expérience',
            'Nom du guide',
            'Email du guide',
            'Nom du voyageur',
            'Email du voyageur',
            'Statut de réservation',
            'Nombre de voyageurs',
            'Type (Groupe / Individuel)',
            'Montant payé par le voyageur (€)',
            'Montant payé au guide (€)',
            'Remboursement ?',
            'Montant remboursé (€)',
            'Annulé par le voyageur ?',
            'Raison de l’annulation',
            'Description de l’annulation',
            'Statut du remboursement'
        ];
    }


}
