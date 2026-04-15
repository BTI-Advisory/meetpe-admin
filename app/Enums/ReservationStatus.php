<?php
namespace App\Enums;
enum ReservationStatus:string  {
    case CREATED= "Crée";
    case ACCEPTÉE="Acceptée";
    case REFUSÉE="Refusée";
    case ANNULÉE="Annulée";
    case ARCHIVÉE="Archivée";
    case PENDING="En attente";
    case ABANDONED="Abondonnée";
}

