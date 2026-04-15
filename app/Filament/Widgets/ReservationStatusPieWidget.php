<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Widgets\ChartWidget;

class ReservationStatusPieWidget extends ChartWidget
{
    protected static ?string $heading = 'Répartition des réservations';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $statuses = [
            ReservationStatus::ACCEPTÉE,
            ReservationStatus::ANNULÉE,
            ReservationStatus::REFUSÉE,
            ReservationStatus::PENDING,
            ReservationStatus::ARCHIVÉE,
        ];

        $data   = [];
        $labels = [];
        $colors = [
            ReservationStatus::ACCEPTÉE->value  => '#22c55e',
            ReservationStatus::ANNULÉE->value   => '#ef4444',
            ReservationStatus::REFUSÉE->value   => '#f97316',
            ReservationStatus::PENDING->value   => '#f59e0b',
            ReservationStatus::ARCHIVÉE->value  => '#6b7280',
        ];

        $bgColors = [];
        foreach ($statuses as $status) {
            $count = Reservation::where('status', $status->value)->count();
            if ($count > 0) {
                $labels[]   = $status->value;
                $data[]     = $count;
                $bgColors[] = $colors[$status->value] ?? '#9ca3af';
            }
        }

        return [
            'datasets' => [
                [
                    'data'            => $data,
                    'backgroundColor' => $bgColors,
                    'hoverOffset'     => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
