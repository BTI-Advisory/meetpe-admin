<?php

namespace App\Filament\Resources\GuideResource\Pages;

use App\Filament\Resources\GuideResource;
use App\Services\DeepLService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditGuide extends EditRecord
{
    protected static string $resource = GuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('voir')
                ->label('Voir le profil')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => GuideResource::getUrl('view', ['record' => $this->record])),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['guide_type'] = optional($this->record->Guide->first())->pro_local ?? 'local';
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Gestion de la photo de profil
        if (empty($data['profile_path'])) {
            // Aucune nouvelle photo → on garde l'existante
            $data['profile_path'] = $this->record->profile_path;
        } elseif (!str_starts_with($data['profile_path'], 'http')) {
            // Filament renvoie le chemin relatif S3 → on construit la full URL
            $data['profile_path'] = Storage::disk('s3')->url($data['profile_path']);
        }

        // Traduire about_me (FR → EN) si le texte a changé
        $newAboutMe = trim($data['about_me'] ?? '');
        if ($newAboutMe !== '' && $newAboutMe !== $this->record->about_me) {
            try {
                $deepl = app(DeepLService::class);
                $translated = $deepl->translateFROMTO($newAboutMe, 'FR', 'EN');
                if ($translated) {
                    $data['about_me_en'] = $translated;
                }
            } catch (\Throwable $e) {
                Log::warning('EditGuide: traduction about_me échouée — ' . $e->getMessage());
            }
        }

        // Sauvegarder le type de guide (pro/local) dans le modèle Guide
        $guide = $this->record->Guide->first();
        if ($guide && isset($data['guide_type'])) {
            $guide->pro_local = $data['guide_type'];
            $guide->save();
        }
        unset($data['guide_type']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Guide mis à jour');
    }
}
