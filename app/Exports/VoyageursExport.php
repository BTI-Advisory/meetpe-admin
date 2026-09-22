<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Voyageur;
use App\Enums\ReservationStatus;
use Illuminate\Support\Facades\DB;

class VoyageursExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        $voyageurs = Voyageur::whereHas('user')
            ->with(['user', 'reservations'])
            ->orderByDesc('created_at')
            ->get();

        $userIds = $voyageurs->pluck('user_id')->filter()->values()->toArray();

        // Charge toutes les réponses en une seule requête
        $allResponses = DB::table('responses')
            ->join('question_choices', 'responses.choice_id', '=', 'question_choices.id')
            ->whereIn('responses.user_id', $userIds)
            ->where('responses.entity', 'voyageur')
            ->select('responses.user_id', 'question_choices.question_id', 'question_choices.choice_txt')
            ->get()
            ->groupBy('user_id');

        $rows = [];

        foreach ($voyageurs as $voyageur) {
            $user        = $voyageur->user;
            $reservations = $voyageur->reservations ?? collect();
            $responses   = $allResponses->get($voyageur->user_id, collect())->groupBy('question_id');

            $get = fn (int $qid) => $responses->get($qid, collect())->pluck('choice_txt')->implode(', ');

            $rows[] = [
                $voyageur->voyageur_id,
                $user->name ?? '',
                $user->email ?? '',
                $user->phone_number ?? '',
                $user->birth_date ?? '',
                $user->age ?? '',
                $voyageur->ville ?? '',
                $voyageur->pays ?? '',
                $user->is_verified_account ? 'Actif' : 'Inactif',
                $user->created_at ? $user->created_at->format('Y-m-d') : '',
                $user->device_language ?? '',

                $get(2),   // Comment tu voyages ?
                $get(5),   // Il y a des sujets qui te plaisent ?
                $get(6),   // Tu maitrises quelles langues ?
                $get(17),  // Comment tu te déplaces ?

                $voyageur->date_arrivee ?? '',
                $voyageur->date_depart ?? '',

                $reservations->count(),
                $reservations->where('status', ReservationStatus::ARCHIVÉE->value)->count(),
                $reservations->where('status', ReservationStatus::ANNULÉE->value)->count(),
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Traveler ID',
            'Nom',
            'Email',
            'Téléphone',
            'Date de naissance',
            'Âge',
            'Ville',
            'Pays',
            'Statut',
            'Date d\'inscription',
            'Langue de l\'app',
            'Comment tu voyages ?',
            'Sujets préférés',
            'Langues',
            'Déplacement',
            'Date d\'arrivée',
            'Date de départ',
            'Nb total réservations',
            'Nb réservations réalisées',
            'Nb réservations annulées',
        ];
    }
}
