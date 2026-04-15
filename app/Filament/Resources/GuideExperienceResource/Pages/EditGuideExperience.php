<?php

namespace App\Filament\Resources\GuideExperienceResource\Pages;

use App\Filament\Resources\GuideExperienceResource;
use App\Models\GuidExperiencePhotos;
use App\Models\Responses;
use App\Services\DeepLService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditGuideExperience extends EditRecord
{
    protected static string $resource = GuideExperienceResource::class;

    /**
     * Avant d'afficher le formulaire :
     * 1. Convertit les response IDs CSV → tableau de choice_ids pour les Select multiple
     * 2. Charge les photos existantes en convertissant les URLs S3 → chemins relatifs
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // ── Catégories & langues ─────────────────────────────────────────────
        foreach (['categorie', 'languages'] as $field) {
            $raw = $this->record->getRawOriginal($field);
            if (!empty($raw)) {
                $responseIds  = array_filter(explode(',', $raw));
                $data[$field] = Responses::whereIn('id', $responseIds)
                    ->pluck('choice_id')
                    ->toArray();
            } else {
                $data[$field] = [];
            }
        }

        // ── Photos ───────────────────────────────────────────────────────────
        $data['photo_principal'] = $this->urlToS3Path($this->record->photoprincipal?->photo_url);
        $data['photo_image_0']   = $this->urlToS3Path($this->record->image_1?->photo_url);
        $data['photo_image_1']   = $this->urlToS3Path($this->record->image_2?->photo_url);
        $data['photo_image_2']   = $this->urlToS3Path($this->record->image_3?->photo_url);
        $data['photo_image_3']   = $this->urlToS3Path($this->record->image_4?->photo_url);
        $data['photo_image_4']   = $this->urlToS3Path($this->record->image_5?->photo_url);

        return $data;
    }

    /**
     * Avant de sauvegarder :
     * 1. Gère les photos (upsert / suppression) puis retire ces champs du payload modèle
     * 2. Reconvertit choice_ids → response IDs CSV pour categorie et languages
     * 3. Traduit en anglais les champs modifiés (title, description, ville, country)
     * 4. Met à jour le timezone si les coordonnées ont changé
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // ── 1. Photos ────────────────────────────────────────────────────────
        $photoMap = [
            'photo_principal' => 'principal',
            'photo_image_0'   => 'image_0',
            'photo_image_1'   => 'image_1',
            'photo_image_2'   => 'image_2',
            'photo_image_3'   => 'image_3',
            'photo_image_4'   => 'image_4',
        ];

        foreach ($photoMap as $formField => $typeImage) {
            $value = $data[$formField] ?? null;
            unset($data[$formField]); // ne pas passer au modèle guide_experiences

            if (empty($value)) {
                // Photo supprimée ou jamais uploadée → supprimer l'enregistrement (sauf principal)
                if ($typeImage !== 'principal') {
                    GuidExperiencePhotos::where('guide_experience_id', $this->record->id)
                        ->where('type_image', $typeImage)
                        ->delete();
                }
                continue;
            }

            // Chemin relatif → URL complète S3
            $fullUrl = str_starts_with($value, 'http')
                ? $value
                : Storage::disk('s3')->url($value);

            GuidExperiencePhotos::updateOrCreate(
                ['guide_experience_id' => $this->record->id, 'type_image' => $typeImage],
                ['photo_url' => $fullUrl]
            );
        }

        // ── 2. Reconversion choice_ids → response IDs ────────────────────────
        $userId = $this->record->user_id;
        foreach (['categorie', 'languages'] as $field) {
            if (!empty($data[$field]) && is_array($data[$field])) {
                $responseIds = [];
                foreach ($data[$field] as $choiceId) {
                    $response = Responses::firstOrCreate(
                        ['user_id' => $userId, 'choice_id' => (int) $choiceId]
                    );
                    $responseIds[] = $response->id;
                }
                $data[$field] = implode(',', $responseIds);
            } else {
                // Rien de sélectionné → conserver la valeur originale pour ne pas violer NOT NULL
                $data[$field] = $this->record->getRawOriginal($field) ?? '';
            }
        }

        // ── 3. Traductions DeepL (uniquement si le champ a changé) ───────────
        try {
            $deepl = app(DeepLService::class);

            $translatable = [
                'title'       => 'title_en',
                'description' => 'description_en',
                'ville'       => 'ville_en',
                'country'     => 'country_en',
            ];

            foreach ($translatable as $frField => $enField) {
                $newValue = trim($data[$frField] ?? '');
                $oldValue = trim($this->record->getRawOriginal($frField) ?? '');

                if ($newValue !== '' && $newValue !== $oldValue) {
                    $translated = $deepl->translateFROMTO($newValue, 'FR', 'EN');
                    if ($translated) {
                        $data[$enField] = $translated;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('EditGuideExperience: DeepL translation failed — ' . $e->getMessage());
        }

        // ── 4. Timezone depuis les coordonnées si elles ont changé ───────────
        $newLat = $data['lat']  ?? null;
        $newLng = $data['lang'] ?? null;
        $oldLat = $this->record->lat;
        $oldLng = $this->record->lang;

        $coordsChanged = !empty($newLat) && !empty($newLng)
            && ((string) $newLat !== (string) $oldLat || (string) $newLng !== (string) $oldLng);

        if ($coordsChanged) {
            $tz = $this->fetchTimezone((float) $newLat, (float) $newLng);
            if ($tz) {
                $data['timezone'] = $tz;
                Log::info('EditGuideExperience: timezone → ' . $tz . ' (expérience #' . $this->record->id . ')');
            }
        }

        return $data;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Convertit une URL S3 complète en chemin relatif compatible FileUpload.
     * Ex: "https://bucket.s3.amazonaws.com/experience_photos/abc.jpg" → "experience_photos/abc.jpg"
     */
    private function urlToS3Path(?string $url): ?string
    {
        if (empty($url) || !str_starts_with($url, 'http')) {
            return $url ?: null;
        }

        // Méthode 1 : utiliser la base URL du disk S3
        $base = rtrim(Storage::disk('s3')->url(''), '/');
        if (!empty($base) && str_starts_with($url, $base . '/')) {
            return substr($url, strlen($base) + 1);
        }

        // Méthode 2 : parse_url + strip bucket name (URL path-style)
        $path   = ltrim(parse_url($url, PHP_URL_PATH) ?? '', '/');
        $bucket = config('filesystems.disks.s3.bucket', '');
        if ($bucket && str_starts_with($path, $bucket . '/')) {
            $path = substr($path, strlen($bucket) + 1);
        }

        return $path ?: null;
    }

    /**
     * Appelle l'API Google Timezone pour obtenir le timezone depuis lat/lng.
     */
    private function fetchTimezone(float $lat, float $lng): ?string
    {
        try {
            $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/timezone/json', [
                'location'  => "{$lat},{$lng}",
                'timestamp' => now()->timestamp,
                'key'       => config('services.google.api_key'),
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                return $response->json('timeZoneId');
            }

            Log::warning('EditGuideExperience: Google Timezone API — status=' . $response->json('status'));
        } catch (\Throwable $e) {
            Log::warning('EditGuideExperience: fetchTimezone failed — ' . $e->getMessage());
        }

        return null;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
