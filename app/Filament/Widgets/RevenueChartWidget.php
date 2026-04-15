<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

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

        $labels = [];
        $ca     = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $start    = Carbon::now()->subMonths($i)->startOfMonth();
            $end      = Carbon::now()->subMonths($i)->endOfMonth();
            $labels[] = $start->translatedFormat('M Y');
            $ca[]     = round(
                Reservation::where('status', ReservationStatus::ACCEPTÉE->value)
                    ->where('is_payed', true)
                    ->whereBetween('date_time', [$start, $end])
                    ->sum('total_price') / 100,
                2
            );
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
