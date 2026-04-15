<?php

namespace App\SubSystems\Notifications\Enums;

enum EnumNotificationType: string
{
    case NOTIFICATION_RESERVATION = 'reservation';
    case NOTIFICATION_MEETPE      = 'notification_meetpe';
}
