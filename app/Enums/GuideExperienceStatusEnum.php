<?php
namespace App\Enums;

enum GuideExperienceStatusEnum: string
{
    //changer toute les status en code
    case ONLINE = "en ligne";
    case TO_BE_COMPLETED = "à compléter";
    case VERFICATION = "en cours de vérification";
    case OFFLINE = "hors ligne";
    case ARCHIVED = "archivée";
    case REFUSED = "refusée";
    case DELETED = "supprimée";
    case DOCUMENT = "autre_document";
}


