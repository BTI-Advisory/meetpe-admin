<?php

namespace App\Filament\Resources\GuideResource\Pages;

use App\Filament\Resources\GuideResource;
use App\Notifications\MailStripeConnectURLForGuide;
use App\Services\StripeService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

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

        ];
    }
}
