<?php

namespace App\Filament\Resources\GuideResource\Pages;

use App\Enums\ReservationStatus;
use App\Filament\Resources\GuideResource;
use App\Models\GuidExperiencePhotos;
use App\Models\GuideExperience;
use App\Notifications\MailStripeConnectURLForGuide;
use App\Services\StripeService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ViewGuide extends ViewRecord
{
    protected static string $resource = GuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Modifier')
                ->icon('heroicon-o-pencil-square'),

Action::make('stripe')
                ->label('Créer compte Stripe')
                ->icon('heroicon-o-credit-card')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Créer le compte Stripe Connect ?')
                ->visible(fn () => $this->record->Guide->first()
                    && optional($this->record->Guide->first())->stripe_connect_form_status !== 'sent')
                ->action(function () {
                    $stripeService = app(StripeService::class);
                    $guide = $this->record->Guide->first();
                    App::setLocale($this->record->device_language ?? 'fr');
                    $result = $stripeService->createStripeAccountForGuide($this->record);
                    if (!is_bool($result)) {
                        $guide->stripe_connect_form_status = 'sent';
                        $guide->save();
                        $this->record->notify(new MailStripeConnectURLForGuide(
                            $this->record->fcm_token,
                            $result
                        ));
                        Log::channel('notification_nails')->info('DASHBOARD_ACTION : MailStripeConnectURLForGuide envoyé à ' . $this->record->email);
                        Notification::make()->title('Compte Stripe créé et lien envoyé')->success()->send();
                    } else {
                        Notification::make()->title('Erreur lors de la création Stripe')->danger()->send();
                    }
                }),

            Action::make('resend_stripe')
                ->label('Renvoyer email Stripe')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Renvoyer l\'email Stripe Connect ?')
                ->visible(fn () => !empty(optional($this->record->Guide->first())->stripe_connect_form_url))
                ->action(function () {
                    $guide = $this->record->Guide->first();
                    App::setLocale($this->record->device_language ?? 'fr');
                    $this->record->notify(new MailStripeConnectURLForGuide(
                        $this->record->fcm_token,
                        $guide->stripe_connect_form_url ?? ''
                    ));
                    Log::channel('notification_nails')->info('DASHBOARD_ACTION : Email Stripe renvoyé à ' . $this->record->email);
                    Notification::make()->title('Email Stripe renvoyé à ' . $this->record->name)->success()->send();
                }),

            Action::make('supprimer_guide')
                ->label('Supprimer définitivement')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Supprimer ce guide définitivement ?')
                ->modalDescription(fn () => 'Action irréversible. Toutes les expériences, photos S3, réponses, réservations et données du compte « ' . $this->record->name . ' » seront supprimées.')
                ->modalSubmitActionLabel('Oui, supprimer définitivement')
                ->visible(function () {
                    $guide = $this->record->Guide->first();

                    $hasActiveReservations = \App\Models\Reservation::whereHas('experience', fn ($q) => $q->where('user_id', $this->record->id))
                        ->whereIn('status', [
                            ReservationStatus::CREATED->value,
                            ReservationStatus::ACCEPTÉE->value,
                            ReservationStatus::PENDING->value,
                        ])->exists();

                    if ($hasActiveReservations) return false;
                    if ($guide?->hasPayoutInProgress()) return false;

                    return true;
                })
                ->action(function () {
                    $user  = $this->record;
                    $guide = $user->Guide->first();

                    $deleteS3 = function (?string $url): void {
                        if (empty($url)) return;
                        try {
                            $base = rtrim(Storage::disk('s3')->url(''), '/');
                            $path = str_starts_with($url, $base . '/')
                                ? substr($url, strlen($base) + 1)
                                : ltrim(parse_url($url, PHP_URL_PATH) ?? '', '/');
                            $bucket = config('filesystems.disks.s3.bucket', '');
                            if ($bucket && str_starts_with($path, $bucket . '/')) {
                                $path = substr($path, strlen($bucket) + 1);
                            }
                            if ($path) Storage::disk('s3')->delete($path);
                        } catch (\Throwable $e) {
                            Log::warning('DeleteGuide: S3 delete failed — ' . $e->getMessage());
                        }
                    };

                    // ── 1. Expériences ────────────────────────────────────────
                    GuideExperience::where('user_id', $user->id)->get()->each(function ($exp) use ($deleteS3) {
                        GuidExperiencePhotos::where('guide_experience_id', $exp->id)->get()
                            ->each(fn ($p) => $deleteS3($p->photo_url));
                        GuidExperiencePhotos::where('guide_experience_id', $exp->id)->delete();

                        $exp->likedExperiences()->delete();
                        DB::table('reservations')->where('experience_id', $exp->id)->delete();

                        $planningIds = $exp->plannings()->pluck('id');
                        if ($planningIds->isNotEmpty()) {
                            DB::table('experience_schedules')->whereIn('planning_id', $planningIds)->delete();
                        }
                        $exp->plannings()->delete();
                        $exp->delete();
                    });

                    // ── 2. Guide ──────────────────────────────────────────────
                    if ($guide) {
                        $guide->failedPayouts()->delete();
                        $guide->delete();
                    }

                    // ── 3. Fichiers S3 + autres données utilisateur ───────────
                    foreach (['profile_path', 'piece_d_identite', 'piece_d_identite_verso', 'KBIS_file', 'about_me_audio'] as $field) {
                        $deleteS3($user->$field);
                    }
                    $user->otherDocuments->each(function ($doc) use ($deleteS3) {
                        $deleteS3($doc->document_path);
                        $doc->delete();
                    });

                    Log::info('DeleteGuide: Guide #' . $user->id . ' (' . $user->email . ') supprimé définitivement par admin');
                    $user->delete();

                    Notification::make()->title('Guide « ' . $user->name . ' » supprimé définitivement')->success()->send();
                    $this->redirect(static::getResource()::getUrl('index'));
                }),

        ];
    }
}
