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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return Cache::remember('dashboard_stats', 300, function () {
            $now        = Carbon::now();
            $start      = $now->copy()->startOfMonth();
            $end        = $now->copy()->endOfMonth();
            $since6     = $now->copy()->subMonths(5)->startOfMonth();
            $statusPaye = [ReservationStatus::ACCEPTÉE->value, ReservationStatus::ARCHIVÉE->value];
            $statusExcl = [ReservationStatus::CREATED->value, ReservationStatus::ABANDONED->value];

            // ── 1 requête : CA + réservations groupés par mois (6 mois) ─────────
            $resByMonth = DB::table('reservations')
                ->selectRaw('YEAR(created_at) as yr, MONTH(created_at) as mo, status, is_payed, COUNT(*) as cnt, SUM(total_price) as total')
                ->where('created_at', '>=', $since6)
                ->groupByRaw('YEAR(created_at), MONTH(created_at), status, is_payed')
                ->get();

            // ── 1 requête : totaux globaux ────────────────────────────────────
            $globalTotals = DB::table('reservations')
                ->selectRaw('status, is_payed, COUNT(*) as cnt, SUM(total_price) as total')
                ->groupByRaw('status, is_payed')
                ->get()
                ->groupBy('status');

            // Calcul CA total et par mois
            $caTotal   = 0;
            $caByMonth = [];
            $resByMonthGrouped = $resByMonth->groupBy(fn($r) => $r->yr . '-' . str_pad($r->mo, 2, '0', STR_PAD_LEFT));

            foreach ($globalTotals as $status => $rows) {
                if (in_array($status, $statusPaye)) {
                    foreach ($rows as $row) {
                        if ($row->is_payed) $caTotal += $row->total;
                    }
                }
            }

            $caParMois  = [];
            $resParMois = [];
            $caMois     = 0;
            $resMois    = 0;

            for ($i = 5; $i >= 0; $i--) {
                $m   = $now->copy()->subMonths($i);
                $key = $m->year . '-' . str_pad($m->month, 2, '0', STR_PAD_LEFT);
                $monthRows = $resByMonthGrouped->get($key, collect());

                $caM  = 0;
                $resM = 0;
                foreach ($monthRows as $row) {
                    if (in_array($row->status, $statusPaye) && $row->is_payed) $caM += $row->total;
                    if (!in_array($row->status, $statusExcl)) $resM += $row->cnt;
                }
                $caParMois[]  = round($caM, 2);
                $resParMois[] = $resM;

                if ($i === 0) {
                    $caMois  = $caM;
                    $resMois = $resM;
                }
            }

            // Taux de conversion depuis les totaux globaux
            $totalRes  = 0;
            $acceptées = 0;
            foreach ($globalTotals as $status => $rows) {
                if (!in_array($status, $statusExcl)) {
                    $totalRes += $rows->sum('cnt');
                }
                if (in_array($status, $statusPaye)) {
                    $acceptées += $rows->sum('cnt');
                }
            }
            $tauxConversion = $totalRes > 0 ? round(($acceptées / $totalRes) * 100, 1) : 0;

            // ── 1 requête : nouveaux guides ce mois ──────────────────────────
            $nouveauxGuides = User::join('guides', 'users.id', '=', 'guides.user_id')
                ->whereBetween('users.created_at', [$start, $end])
                ->count();

            // ── 1 requête : nouveaux voyageurs ce mois ────────────────────────
            $nouveauxVoyageurs = Voyageur::whereBetween('created_at', [$start, $end])->count();

            // ── Compteurs simples (cachés ici, 3 requêtes) ───────────────────
            $totalGuides    = User::join('guides', 'users.id', '=', 'guides.user_id')->count();
            $totalVoyageurs = Voyageur::count();
            $expEnLigne     = GuideExperience::where('status', GuideExperienceStatusEnum::ONLINE->value)->count();
            $expVerif       = GuideExperience::where('status', GuideExperienceStatusEnum::VERFICATION->value)->count();

            return [
                Stat::make('CA Total', number_format($caTotal, 2, ',', ' ') . ' €')
                    ->description('Toutes périodes confondues')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->chart($caParMois),

                Stat::make('CA du mois', number_format($caMois, 2, ',', ' ') . ' €')
                    ->description($now->translatedFormat('F Y'))
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

                Stat::make('Guides', $totalGuides)
                    ->description('+' . $nouveauxGuides . ' ce mois')
                    ->descriptionIcon('heroicon-m-user-plus')
                    ->icon('heroicon-o-user-circle')
                    ->color('primary'),

                Stat::make('Voyageurs', $totalVoyageurs)
                    ->description('+' . $nouveauxVoyageurs . ' ce mois')
                    ->descriptionIcon('heroicon-m-user-plus')
                    ->icon('heroicon-o-users')
                    ->color('info'),

                Stat::make('Expériences en ligne', $expEnLigne)
                    ->icon('heroicon-o-globe-alt')
                    ->color('success'),

                Stat::make('En vérification', $expVerif)
                    ->icon('heroicon-o-clock')
                    ->color('warning'),

                Stat::make('Réservations réalisées', $acceptées)
                    ->icon('heroicon-o-calendar-days')
                    ->color('success'),

                Stat::make('Réservations totales', $totalRes)
                    ->icon('heroicon-o-ticket')
                    ->color('gray'),
            ];
        });
    }
}
