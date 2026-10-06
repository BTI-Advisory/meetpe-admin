<?php

namespace App\Filament\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Illuminate\Support\Facades\Storage;

class DocumentEntry extends Entry
{
    protected string $view = 'filament.infolists.components.document-entry';

    public function getDocumentUrl(): ?string
    {
        $path = $this->getState();
        if (!$path) return null;

        // Already a full URL — use directly to avoid doubling the S3 domain
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        try {
            return Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60));
        } catch (\Exception) {
            return null;
        }
    }

    public function getIsPdf(): bool
    {
        $path = $this->getState();
        return (bool) ($path && str_ends_with(strtolower($path), '.pdf'));
    }
}
