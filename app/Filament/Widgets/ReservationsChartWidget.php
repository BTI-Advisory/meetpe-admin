<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        $since  = Carbon::now()->subMonths($months - 1)->startOfMonth();

        // 1 seule requête GROUP BY au lieu de 3×N requêtes
        $rows = Cache::remember('chart_reservations_' . $months, 300, function () use ($since) {
            return DB::table('reservations')
                ->selectRaw('YEAR(created_at) as yr, MONTH(created_at) as mo, status, COUNT(*) as cnt')
                ->where('created_at', '>=', $since)
                ->groupByRaw('YEAR(created_at), MONTH(created_at), status')
                ->get()
                ->groupBy(fn($r) => $r->yr . '-' . str_pad($r->mo, 2, '0', STR_PAD_LEFT));
        });

        $labels   = [];
        $accepted = [];
        $canceled = [];
        $pending  = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $m         = Carbon::now()->subMonths($i);
            $key       = $m->year . '-' . str_pad($m->month, 2, '0', STR_PAD_LEFT);
            $byStatus  = ($rows->get($key, collect()))->keyBy('status');

            $labels[]   = $m->translatedFormat('M Y');
            $accepted[] = ($byStatus->get(ReservationStatus::ACCEPTÉE->value)?->cnt ?? 0)
                        + ($byStatus->get(ReservationStatus::ARCHIVÉE->value)?->cnt ?? 0);
            $canceled[] = ($byStatus->get(ReservationStatus::ANNULÉE->value)?->cnt ?? 0)
                        + ($byStatus->get(ReservationStatus::REFUSÉE->value)?->cnt ?? 0);
            $pending[]  = $byStatus->get(ReservationStatus::PENDING->value)?->cnt ?? 0;
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
