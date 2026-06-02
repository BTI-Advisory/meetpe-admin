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
                $user->name ?? '',
                $user->email ?? '',
                $user->phone_number ?? '',
                $user->age ?? '',
                $user->created_at ? $user->created_at->format('Y-m-d') : '',

                $get(2),   // Comment tu voyages ?
                $get(5),   // Il y a des sujets qui te plaisent ?
                $get(6),   // Tu maitrises quelles langues ?
                $get(17),  // Comment tu te déplaces ?

                $voyageur->date_arrivee ?? '',
                $voyageur->date_depart ?? '',
                trim(($voyageur->ville ?? '') . ' ' . ($voyageur->pays ?? '')),

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
            'Nom',
            'Email',
            'Téléphone',
            'Âge',
            'Compte créé le',
            'Comment tu voyages ?',
            'Sujets préférés',
            'Langues',
            'Déplacement',
            'Date d\'arrivée',
            'Date de départ',
            'Destination',
            'Nb total réservations',
            'Nb réservations réalisées',
            'Nb réservations annulées',
        ];
    }
}
