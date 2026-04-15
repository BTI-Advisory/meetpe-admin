<?php

namespace App\Filament\Resources\GuideResource\Pages;

use App\Filament\Resources\GuideResource;
use App\Models\Guide;
use App\Models\User;
use App\Services\DeepLService;
use App\Services\UserService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateGuide extends CreateRecord
{
    protected static string $resource = GuideResource::class;

    /**
     * Replique le flux API : /register + /make-profile-guide
     * Le guide est créé comme s'il s'était inscrit depuis l'app.
     */
    protected function handleRecordCreation(array $data): Model
    {
        // 1. Construire la profile_path depuis l'upload S3 Filament
        $profilePath = '';
        if (!empty($data['profile_path'])) {
            // Filament stocke le chemin relatif sur le disk S3 → on construit l'URL complète
            $profilePath = Storage::disk('s3')->url($data['profile_path']);
        }

        // 2. Traduire about_me (FR → EN) via DeepL si renseigné
        $aboutMeEn = null;
        if (!empty(trim($data['about_me'] ?? ''))) {
            try {
                $deepl = app(DeepLService::class);
                $aboutMeEn = $deepl->translateFROMTO($data['about_me'], 'FR', 'EN');
            } catch (\Throwable $e) {
                Log::warning('CreateGuide (admin): DeepL translation failed — ' . $e->getMessage());
            }
        }

        // 3. Créer l'utilisateur (équivalent AuthController::register)
        $user = User::create([
            'name'                => $data['name'],
            'email'               => $data['email'],
            'password'            => Hash::make(Str::random(24)),
            'user_type'           => 'guide',
            'phone_number'        => $data['phone_number'] ?? '',
            'siren_number'        => $data['siren_number'] ?? null,
            'name_of_company'     => $data['name_of_company'] ?? null,
            'is_tva_applicable'   => $data['is_tva_applicable'] ?? false,
            'about_me'            => $data['about_me'] ?? null,
            'about_me_en'         => $aboutMeEn,
            'profile_path'        => $profilePath,
            'rue'                 => $data['rue'] ?? null,
            'ville'               => $data['ville'] ?? null,
            'code_postal'         => $data['code_postal'] ?? null,
            'fcm_token'           => '',
            'is_verified_account' => true,  // compte créé par admin → vérifié d'emblée
            'otp_code'            => rand(1000, 9999),
        ]);

        // 4. Initialiser les paramètres de notifications (équivalent UserService::InitAccount)
        app(UserService::class)->InitAccount($user);

        // 5. Créer le profil Guide (équivalent GuideController::MakeProfileGuide)
        $isP = ($data['guide_type'] ?? 'local') === 'pro';

        Guide::create([
            'user_id'               => $user->id,
            'pro_local'             => $isP ? 'pro' : 'local',
            'guide_truc_de_toi_fr'  => $data['guide_truc_de_toi_fr'] ?? null,
            'personalite_fr'        => $data['personalite_fr'] ?? null,
        ]);

        Log::info('ADMIN: Guide créé manuellement — user_id=' . $user->id . ', email=' . $user->email);

        // Note : pas d'envoi de notifications FCM/email, pas de création Stripe automatique.
        // Le compte Stripe peut être créé depuis la liste des guides via l'action "Créer compte Stripe".

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
