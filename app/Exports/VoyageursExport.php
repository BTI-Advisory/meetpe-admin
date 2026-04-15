<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\GuideExperience;
use App\Models\Voyageur;
use App\Enums\GuideExperienceStatusEnum;
use App\Enums\ReservationStatus;
use App\Models\Responses;

class VoyageursExport implements FromArray, WithHeadings
{


    public function array(): array
    {
        $rows = [];
    
        // Récupération des voyageurs avec leur user + leurs réservations
        $voyageurs = Voyageur::whereHas('user')
            ->with(['user', 'reservations'])
            ->orderByDesc("created_at")
            ->get();
    
        foreach ($voyageurs as $voyageur) {
            $user = $voyageur->user;
            $reservations = $voyageur->reservations ?? collect();
    
            $rows[] = [
                $user->name ?? '',
                $user->email ?? '',
                $user->phone_number ?? '',
                $user->age,
                $user->created_at ? $user->created_at->format('Y-m-d') : '',

                // Réponses multiples formatées
                Responses::getChoicesOf(explode(',', $voyageur->personnalite ?? ''))->pluck('choix')->implode("\n"),
                Responses::getChoicesOf(explode(',', $voyageur->preference ?? ''))->pluck('choix')->implode("\n"),
                Responses::getChoicesOf(explode(',', $voyageur->mode ?? ''))->pluck('choix')->implode("\n"),
                Responses::getChoicesOf(explode(',', $voyageur->type ?? ''))->pluck('choix')->implode("\n"),
                Responses::getChoicesOf(explode(',', $voyageur->languages ?? ''))->pluck('choix')->implode("\n"),
                Responses::getChoicesOf(explode(',', $voyageur->experiences ?? ''))->pluck('choix')->implode("\n"),

                $voyageur->date_arrivee ?? '',
                $voyageur->date_depart ?? '',
                $voyageur->ville.' '.$voyageur->pays,
    
                $reservations->count(),
                $reservations->where('status', ReservationStatus::ARCHIVÉE->value)->count(),
                $reservations->where('status', ReservationStatus::ANNULÉE->value)->count()
            ];
        }
    
        return $rows;
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Email',
            'Téléphone',
            'Âge',
            'Compte crée le',

            'Personnalité',
            'Préferences',
            'Modes',
            'Avec Qui?',
            'Languages',
            'Types des expériences',

            'Dates d\'arrivé',
            'Date de depart',
            'Destination',

            'Nombre total des reservations',
            'Nombre des reservations réalisées',
            'Nombre des reservations annulées'
        ];
    }
}
