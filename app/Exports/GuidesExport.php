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
        $rows[] = [
            $guide->name,
            $guide->email, 
            $guide->phone_number,

            $guide->siren,
            $guide->about,

            $guide->stripe_connect_id,
            $guide->stripe_status,

            $guide->experiences->count(),
            $guide->experiences->where('status', GuideExperienceStatusEnum::ONLINE->value)->count(),

            $reservations->count(),
            $reservations->where('status', ReservationStatus::ARCHIVÉE->value)->count(),
            $reservations->where('status', ReservationStatus::PENDING->value)->count(),
            $reservations->where('status', ReservationStatus::ANNULÉE->value)->count(),
            $reservations->where('status',  ReservationStatus::REFUSÉE->value)->count(),
            $reservations->where('status',  ReservationStatus::ABANDONED->value)->count(),
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

            'SIREN',
            'À propos',

            'Stripe Connect ID',
            'Stripe Status',

            'Nombre total des expériences',
            'Nombre des expériences En ligne',

            'Nombre total des réservations',
            'Nombre total des réservations réalisées avec succès',
            'Nombre total des réservations en attente',
            'Nombre total des réservations annulées',
            'Nombre total des réservations refusées',
            'Nombre total des réservations abondnnées'
        ];
    }
}
