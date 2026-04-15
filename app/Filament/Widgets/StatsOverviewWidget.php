<?php

namespace App\Filament\Widgets;

use App\Enums\GuideExperienceStatusEnum;
use App\Enums\ReservationStatus;
use App\Models\GuideExperience;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Voyageur;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $now   = Carbon::now();
        $start = $now->copy()->startOfMonth();
        $end   = $now->copy()->endOfMonth();

        // CA total (réservations acceptées payées)
        $caTotal = Reservation::where('status', ReservationStatus::ACCEPTÉE->value)
            ->where('is_payed', true)
            ->sum('total_price');

        // CA du mois en cours
        $caMois = Reservation::where('status', ReservationStatus::ACCEPTÉE->value)
            ->where('is_payed', true)
            ->whereBetween('date_time', [$start, $end])
            ->sum('total_price');

        // Évolution CA 6 derniers mois (pour sparkline)
        $caParMois = collect(range(5, 0))->map(function ($i) {
            $s = Carbon::now()->subMonths($i)->startOfMonth();
            $e = Carbon::now()->subMonths($i)->endOfMonth();
            return Reservation::where('status', ReservationStatus::ACCEPTÉE->value)
                ->where('is_payed', true)
                ->whereBetween('date_time', [$s, $e])
                ->sum('total_price');
        })->toArray();

        // Réservations du mois
        $resMois = Reservation::whereNotIn('status', [ReservationStatus::CREATED->value, ReservationStatus::ABANDONED->value])
            ->whereBetween('date_time', [$start, $end])
            ->count();

        // Évolution réservations 6 mois
        $resParMois = collect(range(5, 0))->map(function ($i) {
            $s = Carbon::now()->subMonths($i)->startOfMonth();
            $e = Carbon::now()->subMonths($i)->endOfMonth();
            return Reservation::whereNotIn('status', [ReservationStatus::CREATED->value, ReservationStatus::ABANDONED->value])
                ->whereBetween('date_time', [$s, $e])
                ->count();
        })->toArray();

        // Taux de conversion (acceptées / total hors created+abandoned)
        $totalRes  = Reservation::whereNotIn('status', [ReservationStatus::CREATED->value, ReservationStatus::ABANDONED->value])->count();
        $acceptées = Reservation::where('status', ReservationStatus::ACCEPTÉE->value)->count();
        $tauxConversion = $totalRes > 0 ? round(($acceptées / $totalRes) * 100, 1) : 0;

        // Nouveaux guides ce mois
        $nouveauxGuides = User::whereHas('Guide')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Nouveaux voyageurs ce mois
        $nouveauxVoyageurs = Voyageur::whereBetween('created_at', [$start, $end])->count();

        return [
            Stat::make('CA Total', number_format($caTotal / 100, 2, ',', ' ') . ' €')
                ->description('Toutes périodes confondues')
                ->descriptionIcon('heroicon-m-banknotes')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->chart($caParMois),

            Stat::make('CA du mois', number_format($caMois / 100, 2, ',', ' ') . ' €')
                ->description(Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->icon('heroicon-o-chart-bar')
                ->color('success')
                ->chart($caParMois),

            Stat::make('Taux de conversion', $tauxConversion . ' %')
                ->description('Réservations acceptées / total')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('info'),

            Stat::make('Réservations ce mois', $resMois)
                ->description('Hors créées / abandonnées')
                ->descriptionIcon('heroicon-m-ticket')
                ->icon('heroicon-o-ticket')
                ->color('primary')
                ->chart($resParMois),

            Stat::make('Guides', User::whereHas('Guide')->count())
                ->description('+' . $nouveauxGuides . ' ce mois')
                ->descriptionIcon('heroicon-m-user-plus')
                ->icon('heroicon-o-user-circle')
                ->color('primary'),

            Stat::make('Voyageurs', Voyageur::count())
                ->description('+' . $nouveauxVoyageurs . ' ce mois')
                ->descriptionIcon('heroicon-m-user-plus')
                ->icon('heroicon-o-users')
                ->color('info'),

            Stat::make('Expériences en ligne', GuideExperience::where('status', GuideExperienceStatusEnum::ONLINE->value)->count())
                ->icon('heroicon-o-globe-alt')
                ->color('success'),

            Stat::make('En vérification', GuideExperience::where('status', GuideExperienceStatusEnum::VERFICATION->value)->count())
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Réservations acceptées', $acceptées)
                ->icon('heroicon-o-calendar-days')
                ->color('success'),

            Stat::make('Réservations totales', $totalRes)
                ->icon('heroicon-o-ticket')
                ->color('gray'),
        ];
    }
}
