<?php

namespace App\Filament\Infolist\Components;

use Filament\Infolists\Components\Entry;

class PhotoCarouselEntry extends Entry
{
    protected string $view = 'filament.infolist.components.photo-carousel-entry';

    public function getPhotos(): array
    {
        $record = $this->getRecord();

        if (! $record) {
            return [];
        }

        return \App\Models\GuidExperiencePhotos::where('guide_experience_id', $record->id)
            ->orderByRaw("CASE WHEN type_image = 'principal' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->pluck('photo_url')
            ->filter()
            ->values()
            ->toArray();
    }
}
