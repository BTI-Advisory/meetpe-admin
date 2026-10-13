<?php

namespace App\Observers;

use App\Enums\GuideExperienceStatusEnum;
use App\Models\GuideExperience;
use Illuminate\Support\Facades\Cache;

/**
 * Champs de la home page de l'app (MB-503), mêmes règles que l'API meet-pe-backend
 * (ExperienceObserver et HomeController). Nécessaire ici : quand l'admin modifie une
 * expérience, l'observer de l'API ne s'exécute pas.
 */
class GuideExperienceObserver
{
    /** Caches des sections de la home, partagés avec l'API (même Redis, même préfixe). */
    private const HOME_CACHE_KEYS = [
        'home:featured',
        'home:new',
        'home:week_pick',
        'home:top_rated',
        'home:trending',
    ];

    public function saving(GuideExperience $experience): void
    {
        // Valeur brute : l'accesseur status renvoie le libellé traduit
        $status = $experience->getAttributes()['status'] ?? null;

        // Première mise en ligne : alimente les sections « Nouveautés » et « Sélection de la semaine »
        if ($experience->isDirty('status')
            && $status === GuideExperienceStatusEnum::ONLINE->value
            && is_null($experience->published_at)) {
            $experience->published_at = now();
        }

        // « À la une » : les plus récemment mises en avant apparaissent en premier
        if ($experience->isDirty('is_featured')) {
            $experience->featured_at = $experience->is_featured ? now() : null;
        }
    }

    public function saved(GuideExperience $experience): void
    {
        if ($experience->wasChanged(['status', 'is_featured', 'published_at'])) {
            foreach (self::HOME_CACHE_KEYS as $key) {
                Cache::forget($key);
            }
        }
    }
}
