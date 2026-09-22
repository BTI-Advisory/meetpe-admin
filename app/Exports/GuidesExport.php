<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\GuideExperience;
use App\Models\User;
use App\Enums\GuideExperienceStatusEnum;
use App\Enums\ReservationStatus;
class GuidesExport implements FromArray, WithHeadings
{


public function array(): array
{
    $rows = [];
    $guides = User::whereHas('guide', function ($query) {
        $query->whereNotNull('user_id');
    })
    ->with(['guide', 'experiences.reservations'])
    ->orderByDesc("created_at")
    ->get();

    foreach ($guides as $guide) {

        $experiences = $guide->experiences ?? collect();
        $reservations = $experiences->isNotEmpty()
            ? $experiences->flatMap(function ($experience) {
                return $experience->reservations ?? collect();
            })
            : collect();
        $guideRecord = $guide->guide->first();

        $rows[] = [
            // Identité
            $guideRecord?->guide_id,
            $guide->profile_path,
            $guide->name,
            $guide->email,
            $guide->phone_number,
            $guide->birth_date,

            // Informations légales
            $guide->siren_number,
            $guide->name_of_company,
            $guide->is_tva_applicable ? 'Oui' : 'Non',

            // Stripe
            $guideRecord?->stripe_connect_form_status,
            $guideRecord?->stripe_account_id,

            // Compte
            $guide->is_verified_account ? 'Oui' : 'Non',
            $guide->created_at?->format('d/m/Y'),
            $guide->is_verified_account ? 'Actif' : 'Inactif',

            // Bio
            $guide->about_me,
            $guide->about_me_audio,

            // Statistiques expériences
            $guide->experiences->count(),
            $guide->experiences->where('status', GuideExperienceStatusEnum::ONLINE->value)->count(),

            // Statistiques réservations
            $reservations->count(),
            $reservations->where('status', ReservationStatus::ARCHIVÉE->value)->count(),
            $reservations->where('status', ReservationStatus::PENDING->value)->count(),
            $reservations->where('status', ReservationStatus::ANNULÉE->value)->count(),
            $reservations->where('status', ReservationStatus::REFUSÉE->value)->count(),
            $reservations->where('status', ReservationStatus::ABANDONED->value)->count(),
        ];
    }
    return $rows;
}


    public function headings(): array
    {
        return [
            // Identité
            'Guide ID',
            'Photo (URL)',
            'Nom',
            'Email',
            'Téléphone',
            'Date de naissance',

            // Informations légales
            'SIREN',
            'Société',
            'TVA applicable',

            // Stripe
            'Stripe Connect – Statut',
            'Stripe Account ID',

            // Compte
            'Compte vérifié',
            'Date d\'inscription',
            'Statut du compte',

            // Bio
            'Bio FR',
            'Bio audio (lien)',

            // Statistiques expériences
            'Nombre total des expériences',
            'Nombre des expériences En ligne',

            // Statistiques réservations
            'Nombre total des réservations',
            'Nombre total des réservations réalisées avec succès',
            'Nombre total des réservations en attente',
            'Nombre total des réservations annulées',
            'Nombre total des réservations refusées',
            'Nombre total des réservations abandonnées',
        ];
    }
}
