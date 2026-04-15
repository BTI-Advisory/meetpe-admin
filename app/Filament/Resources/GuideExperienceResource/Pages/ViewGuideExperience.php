<?php

namespace App\Filament\Resources\GuideExperienceResource\Pages;

use App\Enums\GuideExperienceStatusEnum;
use App\Filament\Resources\GuideExperienceResource;
use App\Models\User;
use App\Notifications\DocumentsSupplementaires;
use App\Notifications\MakeExperienceNonComplete;
use App\Notifications\YourExperienceIsValid;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\App;

class ViewGuideExperience extends ViewRecord
{
    protected static string $resource = GuideExperienceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Modifier')
                ->icon('heroicon-o-pencil-square'),

            Action::make('accepter')
                ->label('Accepter')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Valider cette expérience ?')
                ->visible(fn () => in_array($this->record->status, [
                    GuideExperienceStatusEnum::VERFICATION->value,
                    GuideExperienceStatusEnum::TO_BE_COMPLETED->value,
                    GuideExperienceStatusEnum::DOCUMENT->value,
                    GuideExperienceStatusEnum::OFFLINE->value,
                ]))
                ->action(function () {
                    $this->record->status = GuideExperienceStatusEnum::ONLINE->value;
                    $this->record->save();
                    $user = User::find($this->record->user_id);
                    App::setLocale($user->device_language ?? 'fr');
                    $user->notify(new YourExperienceIsValid(
                        $user->fcm_token,
                        $this->record->getTitleForLocale($user->device_language ?? 'fr')
                    ));
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Expérience validée')->success()->send();
                }),

            Action::make('refuser')
                ->label('Refuser')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Refuser cette expérience ?')
                ->visible(fn () => $this->record->status !== GuideExperienceStatusEnum::REFUSED->value)
                ->action(function () {
                    $this->record->status = GuideExperienceStatusEnum::REFUSED->value;
                    $this->record->save();
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Expérience refusée')->success()->send();
                }),

            Action::make('commenter')
                ->label('À compléter')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('warning')
                ->form([
                    Textarea::make('raison')
                        ->label('Raison à communiquer au guide')
                        ->required()
                        ->rows(4),
                ])
                ->action(function (array $data) {
                    $this->record->raison = $data['raison'];
                    $this->record->status = GuideExperienceStatusEnum::TO_BE_COMPLETED->value;
                    $this->record->save();
                    $user = User::find($this->record->user_id);
                    App::setLocale($user->device_language ?? 'fr');
                    $user->notify(new MakeExperienceNonComplete(
                        $data['raison'],
                        $this->record->getTitleForLocale($user->device_language ?? 'fr'),
                        $user->fcm_token
                    ));
                    $this->refreshFormData(['status', 'raison']);
                    Notification::make()->title('Notification envoyée au guide')->success()->send();
                }),

            Action::make('autre_document')
                ->label('Doc. supplémentaire')
                ->icon('heroicon-o-document-plus')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Demander un document supplémentaire ?')
                ->visible(fn () => $this->record->status !== GuideExperienceStatusEnum::DOCUMENT->value)
                ->action(function () {
                    $this->record->status = GuideExperienceStatusEnum::DOCUMENT->value;
                    $this->record->save();
                    $user = User::find($this->record->user_id);
                    App::setLocale($user->device_language ?? 'fr');
                    $user->notify(new DocumentsSupplementaires(
                        $user->fcm_token,
                        $this->record->getTitleForLocale($user->device_language ?? 'fr')
                    ));
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Demande envoyée')->success()->send();
                }),

            Action::make('archiver')
                ->label('Archiver')
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Archiver cette expérience ?')
                ->modalDescription('L\'expérience sera marquée comme supprimée. Elle restera dans la base de données.')
                ->visible(fn () => $this->record->status !== GuideExperienceStatusEnum::DELETED->value)
                ->action(function () {
                    $this->record->status = GuideExperienceStatusEnum::DELETED->value;
                    $this->record->save();
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Expérience archivée')->success()->send();
                }),

            Action::make('fichiers')
                ->label('Fichiers')
                ->icon('heroicon-o-folder-open')
                ->color('gray')
                ->url(fn () => route('guidesFiles', $this->record->user_id))
                ->openUrlInNewTab(),
        ];
    }
}
