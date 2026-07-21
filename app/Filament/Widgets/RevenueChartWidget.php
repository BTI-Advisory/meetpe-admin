<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Chiffre d\'affaires par mois (€)';
    protected static ?int $sort = 3;
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

        // 1 seule requête GROUP BY au lieu de N requêtes
        $rows = Cache::remember('chart_revenue_' . $months, 300, function () use ($since) {
            return DB::table('reservations')
                ->selectRaw('YEAR(created_at) as yr, MONTH(created_at) as mo, SUM(total_price) as total')
                ->whereIn('status', [ReservationStatus::ACCEPTÉE->value, ReservationStatus::ARCHIVÉE->value])
                ->where('is_payed', true)
                ->where('created_at', '>=', $since)
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->get()
                ->keyBy(fn($r) => $r->yr . '-' . str_pad($r->mo, 2, '0', STR_PAD_LEFT));
        });

        $labels = [];
        $ca     = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $m        = Carbon::now()->subMonths($i);
            $key      = $m->year . '-' . str_pad($m->month, 2, '0', STR_PAD_LEFT);
            $labels[] = $m->translatedFormat('M Y');
            $ca[]     = round($rows->get($key)?->total ?? 0, 2);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'CA (€)',
                    'data'            => $ca,
                    'backgroundColor' => 'rgba(255,76,0,0.7)',
                    'borderColor'     => '#FF4C00',
                    'borderWidth'     => 2,
                    'borderRadius'    => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
