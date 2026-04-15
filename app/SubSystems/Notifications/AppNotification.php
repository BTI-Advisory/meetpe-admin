<?php

namespace App\SubSystems\Notifications;

use Illuminate\Support\Facades\DB;

class AppNotification
{
    /**
     * Retourne les canaux de notification autorisés pour un utilisateur donné.
     *
     * Les préférences sont stockées dans la table notification_settings.
     * Convention des colonnes : {type}_email, {type}_app, {type}_sms
     * où {type} correspond à la valeur de l'enum EnumNotificationType.
     *
     * Si aucun paramètre trouvé, on envoie par mail par défaut.
     *
     * @param  string  $email            Email de l'utilisateur
     * @param  string  $notificationType Valeur de l'enum (ex: 'reservation', 'notification_meetpe')
     * @return array<int, string>
     */
    public function AllowedChannelsByUser(string $email, string $notificationType): array
    {
        $user = DB::table('users')->where('email', $email)->first();

        if (! $user) {
            return ['mail'];
        }

        $settings = DB::table('notification_settings')
            ->where('user_id', $user->id)
            ->first();

        if (! $settings) {
            return ['mail'];
        }

        $channels = [];

        $emailColumn = $notificationType . '_email';
        if (isset($settings->$emailColumn) && $settings->$emailColumn) {
            $channels[] = 'mail';
        }

        // Le canal app (FCM) est géré directement dans chaque notification
        // via array_push($channels, FcmChannel::class) selon le fcm_token disponible.
        // On ne l'ajoute pas ici pour éviter les doublons.

        // Si aucun canal activé, on fallback sur mail pour ne pas perdre la notification
        if (empty($channels)) {
            $channels[] = 'mail';
        }

        return $channels;
    }
}
