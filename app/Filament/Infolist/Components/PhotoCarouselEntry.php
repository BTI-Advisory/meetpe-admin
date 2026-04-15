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

        $record->loadMissing(['photoprincipal', 'image_1', 'image_2', 'image_3', 'image_4', 'image_5']);

        return collect([
            $record->photoprincipal?->photo_url,
            $record->image_1?->photo_url,
            $record->image_2?->photo_url,
            $record->image_3?->photo_url,
            $record->image_4?->photo_url,
            $record->image_5?->photo_url,
        ])->filter()->values()->toArray();
    }
}
