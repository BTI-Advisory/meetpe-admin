<?php

namespace App\Exports;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncompleteReservationsExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, WithStyles
{
    public function query()
    {
        return Reservation::with(['experience:id,title,ville', 'voyageur:id,name,email'])
            ->select(
                'id', 'voyageur_id', 'experience_id',
                'created_at', 'date_time',
                'nombre_des_voyageurs', 'total_price',
                'stripe_payment_intent_id', 'stripe_payment_error',
                'status',
            )
            ->whereIn('status', [
                ReservationStatus::CREATED->value,
                ReservationStatus::ABANDONED->value,
            ])
            ->latest();
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function headings(): array
    {
        return [
            'Reservation ID',
            'Traveler ID',
            'Nom du voyageur',
            'Email du voyageur',
            'Experience ID',
            'Titre de l\'experience',
            'Ville',
            'Date de la tentative',
            'Date / heure du creneau vise',
            'Nombre de participants',
            'Montant potentiel (EUR)',
            'Statut',
            'Etape estimee',
        ];
    }

    public function map($r): array
    {
        return [
            $r->id,
            $r->voyageur_id,
            $r->voyageur?->name ?? '',
            $r->voyageur?->email ?? '',
            $r->experience_id,
            $r->experience?->title ?? '',
            $r->experience?->ville ?? '',
            $r->created_at?->format('d/m/Y H:i'),
            $r->date_time ? \Carbon\Carbon::parse($r->date_time)->format('d/m/Y H:i') : '',
            $r->nombre_des_voyageurs,
            $r->total_price,
            $r->status,
            $this->estimateStep($r),
        ];
    }

    private function estimateStep($r): string
    {
        if ($r->stripe_payment_error) {
            return 'Echec paiement : ' . $r->stripe_payment_error;
        }

        if ($r->stripe_payment_intent_id) {
            return 'Paiement initie, non finalise';
        }

        return 'Abandon avant paiement';
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '374151']],
        ]);

        return [];
    }
}
