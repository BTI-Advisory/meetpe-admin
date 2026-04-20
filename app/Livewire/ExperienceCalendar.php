<?php

namespace App\Livewire;

use App\Models\GuideExperience;
use App\Models\Reservation;
use Carbon\Carbon;
use Livewire\Component;

class ExperienceCalendar extends Component
{
    public int $experienceId;

    public function render()
    {
        $record   = GuideExperience::find($this->experienceId);
        $capacite = (int) ($record->nombre_des_voyageur ?? 0);
        $today    = now()->format('Y-m-d');
        $events   = [];
        $firstFuture = null;

        $plannings = $record->plannings()
            ->with('schedules')
            ->orderBy('start_date')
            ->get();

        $allReservations = Reservation::where('experience_id', $this->experienceId)
            ->where('is_payed', true)
            ->where('status', 'Acceptée')
            ->get();

        foreach ($plannings as $planning) {
            $date = $planning->start_date;

            foreach ($planning->schedules as $schedule) {
                $startTime = $schedule->start_time;
                $endTime   = $schedule->end_time;

                $slotRes = $allReservations->filter(function ($r) use ($date, $startTime) {
                    $dt = Carbon::parse($r->date_time);
                    return $dt->format('Y-m-d') === $date
                        && $dt->format('H:i:s') === $startTime;
                });

                $totalReserved = (int) $slotRes->sum('nombre_des_voyageurs');
                $isGroup       = $slotRes->where('is_group', true)->isNotEmpty();
                $remaining     = max(0, $capacite - $totalReserved);
                $isPast        = $date < $today;

                if (! $firstFuture && ! $isPast) {
                    $firstFuture = $date;
                }

                $colors = match (true) {
                    $isPast                      => ['bg' => '#f3f4f6', 'text' => '#9ca3af', 'border' => '#d1d5db'],
                    $isGroup || $remaining === 0 => ['bg' => '#fee2e2', 'text' => '#dc2626', 'border' => '#f87171'],
                    $totalReserved === 0         => ['bg' => '#dcfce7', 'text' => '#16a34a', 'border' => '#4ade80'],
                    default                      => ['bg' => '#ffedd5', 'text' => '#ea580c', 'border' => '#fb923c'],
                };

                $startF = substr($startTime, 0, 5);
                $endF   = substr($endTime, 0, 5);

                $statusLabel = match (true) {
                    $isPast                      => 'Passé',
                    $isGroup || $remaining === 0 => $isGroup ? 'Groupe complet' : 'Complet',
                    $totalReserved === 0         => 'Disponible',
                    default                      => 'Partiel',
                };

                $events[] = [
                    'title'           => $startF . '–' . $endF . ($totalReserved > 0 ? " ({$totalReserved}/{$capacite})" : ''),
                    'start'           => $date . 'T' . $startTime,
                    'end'             => $date . 'T' . $endTime,
                    'backgroundColor' => $colors['bg'],
                    'textColor'       => $colors['text'],
                    'borderColor'     => $colors['border'],
                    'extendedProps'   => [
                        'dateLabel'     => Carbon::parse($date)->translatedFormat('l d F Y'),
                        'horaire'       => $startF . ' – ' . $endF,
                        'totalReserved' => $totalReserved,
                        'capacite'      => $capacite,
                        'remaining'     => $remaining,
                        'isGroup'       => $isGroup,
                        'isPast'        => $isPast,
                        'statusLabel'   => $statusLabel,
                    ],
                ];
            }
        }

        return view('livewire.experience-calendar', [
            'calId'       => 'exp-cal-' . $this->experienceId,
            'events'      => $events,
            'initialDate' => $firstFuture ?? $today,
            'totalSlots'  => count($events),
        ]);
    }
}
