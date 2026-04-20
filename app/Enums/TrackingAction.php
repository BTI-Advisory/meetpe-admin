<?php

namespace App\Enums;

enum TrackingAction: string
{
    case USER_LOGIN = 'user.login';
    case USER_REGISTER = 'user.register';
    case PROFILE_SET = 'profile.set';
    case PROFILE_MAKE = 'profile.make';
    case PROFILE_MAKE_STEP_2 = 'profile.makestep2';

    case EXPERIENCE_CREATE = 'experience.make';
    case EXPERIENCE_UPDATE = 'experience.update';
    case EXPERIENCE_DELETE = 'experience.delete';

    case RESERVATION_CREATE = 'reservation.register';
    case RESERVATION_UPDATE = 'reservation.update';
    case RESERVATION_CANCEL = 'reservation.cancel';

    public function label(): string
    {
        return match ($this) {
            self::USER_LOGIN             => 'Connexion utilisateur',
            self::USER_REGISTER          => 'Inscription utilisateur',
            self::PROFILE_SET            => 'Profil',
            self::PROFILE_MAKE           => 'Onboarding',
            self::PROFILE_MAKE_STEP_2    => 'Onboarding étape 2',
            self::EXPERIENCE_CREATE      => "Création de l'expérience",
            self::EXPERIENCE_UPDATE      => "Mise à jour de l'expérience",
            self::EXPERIENCE_DELETE      => "Suppression de l'expérience",
            self::RESERVATION_CREATE     => 'Création de réservation',
            self::RESERVATION_UPDATE     => 'Mise à jour de réservation',
            self::RESERVATION_CANCEL     => 'Annulation de réservation',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::USER_LOGIN,
            self::EXPERIENCE_UPDATE,
            self::PROFILE_SET,
            self::PROFILE_MAKE,
            self::PROFILE_MAKE_STEP_2,
            self::RESERVATION_UPDATE  => 'info',

            self::USER_REGISTER,
            self::EXPERIENCE_CREATE,
            self::RESERVATION_CREATE  => 'success',

            self::EXPERIENCE_DELETE,
            self::RESERVATION_CANCEL  => 'danger',
        };
    }
}
