<?php

namespace App\Filament\Resources\VoyageurResource\Pages;

use App\Enums\ReservationStatus;
use App\Filament\Resources\VoyageurResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ViewVoyageur extends ViewRecord
{
    protected static string $resource = VoyageurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('supprimer_voyageur')
                ->label('Supprimer définitivement')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Supprimer ce voyageur définitivement ?')
                ->modalDescription(fn () => 'Action irréversible. Le compte, les réservations, les avis et toutes les données de « ' . ($this->record->user?->name ?? '—') . ' » seront supprimés.')
                ->modalSubmitActionLabel('Oui, supprimer définitivement')
                ->visible(function () {
                    $userId = $this->record->user_id;

                    $hasActiveReservations = \App\Models\Reservation::where('voyageur_id', $userId)
                        ->whereIn('status', [
                            ReservationStatus::ACCEPTÉE->value,
                            ReservationStatus::PENDING->value,
                        ])->exists();

                    return !$hasActiveReservations;
                })
                ->action(function () {
                    $voyageur = $this->record;
                    $user     = $voyageur->user;

                    if (!$user) {
                        Notification::make()->title('Utilisateur introuvable')->danger()->send();
                        return;
                    }

                    $userId  = $user->id;
                    $isGuide = DB::table('guides')->where('user_id', $userId)->exists();

                    DB::beginTransaction();
                    try {
                        // ── 1. Réservations en tant que voyageur ─────────────────
                        DB::table('reservations')->where('voyageur_id', $userId)->delete();

                        // ── 2. Avis rédigés par ce voyageur ──────────────────────
                        DB::table('avis')->where('user_id', $userId)->delete();

                        // ── 3. Réponses questionnaire voyageur ────────────────────
                        DB::table('responses')
                            ->where('user_id', $userId)
                            ->where('entity', 'voyageur')
                            ->delete();

                        // ── 4. Expériences likées ─────────────────────────────────
                        DB::table('liked_experiences')->where('user_id', $userId)->delete();

                        // ── 5. Trackings ──────────────────────────────────────────
                        DB::table('user_trackings')->where('user_id', $userId)->delete();
                        DB::table('user_trackings_archive')->where('user_id', $userId)->delete();

                        if (!$isGuide) {
                            // Nettoyer toutes les FK liées au user AVANT $voyageur->delete()
                            // pour éviter tout cascade implicite vers users
                            DB::table('chat_messages')->where('sender_id', $userId)->delete();
                            DB::table('chat_channel_users')->where('user_id', $userId)->delete();
                            DB::table('notification_settings')->where('user_id', $userId)->delete();
                            DB::table('user_d_evices')->where('user_id', $userId)->delete();
                            DB::table('user_roles')->where('user_id', $userId)->delete();
                            DB::table('contacts')->where('user_id', $userId)->delete();
                            DB::table('user_autofacturation_consents')->where('user_id', $userId)->delete();
                        }

                        // ── 6. Profil voyageur ────────────────────────────────────
                        $voyageur->delete();

                        if ($isGuide) {
                            Log::info('DeleteVoyageur: profil voyageur de user_id=' . $userId . ' (' . $user->email . ') supprimé — compte guide conservé');

                            DB::commit();

                            Notification::make()
                                ->title('Profil voyageur de « ' . $user->name . ' » supprimé — compte guide conservé')
                                ->success()
                                ->send();
                        } else {
                            $deleteS3 = function (?string $url): void {
                                if (empty($url) || str_starts_with($url, 'http')) return;
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
                                    Log::warning('DeleteVoyageur: S3 delete failed — ' . $e->getMessage());
                                }
                            };

                            $deleteS3($user->profile_path);

                            Log::info('DeleteVoyageur: user_id=' . $userId . ' (' . $user->email . ') supprimé définitivement par admin');
                            $user->delete();

                            DB::commit();

                            Notification::make()
                                ->title('Voyageur « ' . $user->name . ' » supprimé définitivement')
                                ->success()
                                ->send();
                        }

                        $this->redirect(static::getResource()::getUrl('index'));

                    } catch (\Throwable $e) {
                        DB::rollBack();
                        Log::error('DeleteVoyageur: échec suppression user_id=' . $userId . ' — ' . $e->getMessage());

                        Notification::make()
                            ->title('Erreur lors de la suppression')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
