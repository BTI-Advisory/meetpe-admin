<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ReservationsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Réservations par mois';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '250px';
    public ?string $filter = '6';

    protected function getFilters(): ?array
    {
        return [
            '3'  => '3 derniers mois',
            '6'  => '6 derniers mois',
            '12' => '12 derniers mois',
        ];
    }

    protected function getData(): array
    {
        $months = (int) ($this->filter ?? 6);

        $labels   = [];
        $accepted = [];
        $canceled = [];
        $pending  = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end   = Carbon::now()->subMonths($i)->endOfMonth();

            $labels[]   = $start->translatedFormat('M Y');
            $accepted[] = Reservation::where('status', ReservationStatus::ACCEPTÉE->value)
                ->whereBetween('date_time', [$start, $end])->count();
            $canceled[] = Reservation::whereIn('status', [ReservationStatus::ANNULÉE->value, ReservationStatus::REFUSÉE->value])
                ->whereBetween('date_time', [$start, $end])->count();
            $pending[]  = Reservation::where('status', ReservationStatus::PENDING->value)
                ->whereBetween('date_time', [$start, $end])->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Acceptées',
                    'data'            => $accepted,
                    'borderColor'     => '#22c55e',
                    'backgroundColor' => 'rgba(34,197,94,0.15)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Annulées / Refusées',
                    'data'            => $canceled,
                    'borderColor'     => '#ef4444',
                    'backgroundColor' => 'rgba(239,68,68,0.10)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'En attente',
                    'data'            => $pending,
                    'borderColor'     => '#f59e0b',
                    'backgroundColor' => 'rgba(245,158,11,0.10)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
