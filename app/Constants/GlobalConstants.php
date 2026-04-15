<?php

namespace App\Constants;

class GlobalConstants
{
    public const MATCHING_CRITERIA_WEIGHTS = [
        'languages' => 0.15,
        'categories' => 0.2,
        'dates' => 0.3,
        'location' => 0.35,
    ];
    public const RADIUS = 100;
    public const VOYAGEUR_EXPERIENCE_DECOUVRIR = "Pas d’idée, fais-moi découvrir";
}