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
        $record = GuideExperience::find($this->experienceId);

        if (! $record) {
            return view('livewire.experience-calendar', [
                'calId'       => 'exp-cal-' . $this->experienceId,
                'events'      => [],
                'initialDate' => now()->format('Y-m-d'),
                'totalSlots'  => 0,
            ]);
        }

        $capacite    = (int) ($record->nombre_des_voyageur ?? 0);
        $isGroupOnly = $capacite === 0; // experience réservable uniquement en groupe privé
        $today       = now()->format('Y-m-d');
        $events      = [];
        $firstFuture = null;

        $plannings = $record->plannings()
            ->with('schedules')
            ->orderBy('start_date')
            ->get();

        $allReservations = Reservation::where('experience_id', $this->experienceId)
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
                $isPast        = $date < $today;

                if ($isGroupOnly) {
                    // Créneau complet uniquement si une résa groupe privé existe déjà
                    $isFull    = $isGroup;
                    $remaining = null;
                } else {
                    $remaining = max(0, $capacite - $totalReserved);
                    $isFull    = $isGroup || $remaining === 0;
                }

                if (! $firstFuture && ! $isPast) {
                    $firstFuture = $date;
                }

                $colors = match (true) {
                    $isPast          => ['bg' => '#f3f4f6', 'text' => '#9ca3af', 'border' => '#d1d5db'],
                    $isFull          => ['bg' => '#fee2e2', 'text' => '#dc2626', 'border' => '#f87171'],
                    $totalReserved === 0 => ['bg' => '#dcfce7', 'text' => '#16a34a', 'border' => '#4ade80'],
                    default          => ['bg' => '#ffedd5', 'text' => '#ea580c', 'border' => '#fb923c'],
                };

                $startF = substr($startTime, 0, 5);
                $endF   = substr($endTime, 0, 5);

                $statusLabel = match (true) {
                    $isPast          => 'Passé',
                    $isGroup         => 'Groupe complet',
                    $isFull          => 'Complet',
                    $totalReserved === 0 => 'Disponible',
                    default          => 'Partiel',
                };

                if ($isGroupOnly) {
                    $titleSuffix = $isGroup ? ' (groupe)' : '';
                } else {
                    $titleSuffix = $totalReserved > 0 ? " ({$totalReserved}/{$capacite})" : '';
                }

                $events[] = [
                    'title'           => $startF . '–' . $endF . $titleSuffix,
                    'start'           => $date . 'T' . $startTime,
                    'end'             => $date . 'T' . $endTime,
                    'backgroundColor' => $colors['bg'],
                    'textColor'       => $colors['text'],
                    'borderColor'     => $colors['border'],
                    'extendedProps'   => [
                        'dateLabel'     => Carbon::parse($date)->translatedFormat('l d F Y'),
                        'horaire'       => $startF . ' – ' . $endF,
                        'totalReserved' => $totalReserved,
                        'capacite'      => $isGroupOnly ? null : $capacite,
                        'remaining'     => $remaining,
                        'isGroup'       => $isGroup,
                        'isGroupOnly'   => $isGroupOnly,
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
