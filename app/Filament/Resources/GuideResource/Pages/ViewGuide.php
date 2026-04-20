<?php

namespace App\Filament\Resources\GuideResource\Pages;

use App\Filament\Resources\GuideResource;
use App\Models\FailedPayout;
use App\Models\Guide;
use App\Models\Payout;
use App\Notifications\MailStripeConnectURLForGuide;
use App\Services\StripeService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Transfer;

class ViewGuide extends ViewRecord
{
    protected static string $resource = GuideResource::class;

    protected function resolveRecord(int|string $key): Model
    {
        return \App\Models\User::with([
            'Guide.failedPayouts',
            'Guide.payouts',
            'devices',
            'otherDocuments',
        ])->findOrFail($key);
    }

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

            Action::make('retry_payout')
                ->label('Relancer un payout')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->visible(fn () => $this->record->Guide->first()?->failedPayouts->isNotEmpty())
                ->form([
                    Forms\Components\Select::make('failed_payout_id')
                        ->label('Payout à relancer')
                        ->options(function () {
                            return $this->record->Guide->first()?->failedPayouts
                                ->mapWithKeys(fn ($fp) => [
                                    $fp->id => "{$fp->month} — " . number_format($fp->payout_amount, 2) . ' € — ' . ($fp->failure_message ?? ''),
                                ]) ?? [];
                        })
                        ->required(),
                ])
                ->action(function (array $data) {
                    $failed = FailedPayout::findOrFail($data['failed_payout_id']);
                    $guide  = $this->record->Guide->first();

                    if (!$guide?->stripe_account_id) {
                        Notification::make()->title('Compte Stripe non configuré')->danger()->send();
                        return;
                    }

                    try {
                        Stripe::setApiKey(config('services.stripe.secret'));
                        $transfer = Transfer::create([
                            'amount'      => $failed->payout_amount * 100,
                            'currency'    => 'eur',
                            'destination' => $guide->stripe_account_id,
                            'description' => 'Régularisation payout ' . $failed->month,
                        ]);

                        Payout::create([
                            'guide_id'           => $guide->guide_id,
                            'amount'             => $failed->payout_amount,
                            'stripe_transfer_id' => $transfer->id,
                            'paid_at'            => now(),
                            'payment_period'     => $failed->month,
                        ]);

                        $failed->delete();
                        Log::channel('stripe_payout')->info('DASHBOARD_RETRY_SUCCESS | guide=' . $guide->guide_id . ' | mois=' . $failed->month);
                        Notification::make()->title('Payout relancé avec succès')->success()->send();
                        $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                    } catch (\Exception $e) {
                        Log::channel('stripe_payout')->error('DASHBOARD_RETRY_ERROR | ' . $e->getMessage());
                        Notification::make()->title('Erreur : ' . $e->getMessage())->danger()->send();
                    }
                }),
        ];
    }

    public static function decodeMetadata(mixed $metadata): string
    {
        if (empty($metadata)) return '—';
        try {
            $raw          = is_string($metadata) ? $metadata : json_encode($metadata);
            $decompressed = @gzuncompress(base64_decode($raw));
            $data         = $decompressed !== false
                ? json_decode($decompressed, true)
                : (is_array($metadata) ? $metadata : json_decode($raw, true));

            if (!is_array($data)) return '—';

            $lines = [];
            if (!empty($data['full_url'])) $lines[] = 'URL : ' . $data['full_url'];
            if (!empty($data['query']))    $lines[] = 'Query : ' . json_encode($data['query'], JSON_UNESCAPED_UNICODE);
            if (!empty($data['payload']))  $lines[] = 'Payload : ' . json_encode($data['payload'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            return implode("\n", $lines) ?: '—';
        } catch (\Throwable) {
            return '—';
        }
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Section::make('Profil')
                ->icon('heroicon-o-user')
                ->collapsible()
                ->schema([
                    Grid::make(4)->schema([
                        ImageEntry::make('profile_path')
                            ->label('Photo')
                            ->disk('s3')
                            ->circular()
                            ->height(80)
                            ->columnSpanFull(),
                        TextEntry::make('name')->label('Nom'),
                        TextEntry::make('email')->label('Email')->copyable(),
                        TextEntry::make('phone_number')->label('Téléphone')->placeholder('—'),
                        TextEntry::make('birth_date')->label('Date de naissance')
                            ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y') : '—'),
                        TextEntry::make('guide_type')
                            ->label('Type')
                            ->badge()
                            ->state(fn ($record) => optional($record->Guide->first())->pro_local === 'pro' ? 'Professionnel' : 'Local')
                            ->color(fn ($state) => $state === 'Professionnel' ? 'info' : 'gray'),
                        TextEntry::make('siren_number')->label('SIREN')->placeholder('—'),
                        TextEntry::make('name_of_company')->label('Société')->placeholder('—'),
                        TextEntry::make('is_tva_applicable')->label('TVA')
                            ->state(fn ($record) => $record->is_tva_applicable ? 'Assujetti' : 'Non assujetti')
                            ->badge()
                            ->color(fn ($state) => $state === 'Assujetti' ? 'warning' : 'gray'),
                        TextEntry::make('stripe_status')
                            ->label('Stripe Connect')
                            ->badge()
                            ->state(fn ($record) => optional($record->Guide->first())->stripe_connect_form_status)
                            ->color(fn (?string $state) => match ($state) {
                                'sent'    => 'success',
                                'pending' => 'warning',
                                default   => 'gray',
                            })
                            ->placeholder('Non configuré'),
                        TextEntry::make('stripe_account_id')
                            ->label('Stripe Account ID')
                            ->state(fn ($record) => optional($record->Guide->first())->stripe_account_id)
                            ->copyable()
                            ->placeholder('—'),
                        TextEntry::make('is_verified_account')
                            ->label('Compte vérifié')
                            ->state(fn ($record) => $record->is_verified_account ? 'Vérifié' : 'Non vérifié')
                            ->badge()
                            ->color(fn ($state) => $state === 'Vérifié' ? 'success' : 'danger'),
                        TextEntry::make('created_at')->label('Inscrit le')->date('d/m/Y'),
                    ]),
                    TextEntry::make('about_me')->label('Bio (FR)')->placeholder('—')->columnSpanFull(),
                ]),

            Section::make('Réponses au questionnaire')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->collapsible()
                ->schema([
                    RepeatableEntry::make('questionnaire')
                        ->label('')
                        ->getStateUsing(function ($record) {
                            $guideId = DB::table('guides')->where('user_id', $record->id)->value('guide_id');
                            if (!$guideId) return [];

                            return DB::table('responses')
                                ->join('question_choices', 'responses.choice_id', '=', 'question_choices.id')
                                ->join('questions', 'question_choices.question_id', '=', 'questions.id')
                                ->where('responses.entity', 'guide')
                                ->where('responses.entity_id', $guideId)
                                ->select('questions.id as question_id', 'questions.question_text as question', 'question_choices.choice_txt as reponse')
                                ->orderBy('questions.id')
                                ->get()
                                ->groupBy('question')
                                ->map(fn ($rows, $question) => [
                                    'question' => $question,
                                    'reponses' => $rows->pluck('reponse')->toArray(),
                                ])
                                ->values()
                                ->toArray();
                        })
                        ->schema([
                            TextEntry::make('question')->label('Question'),
                            TextEntry::make('reponses')->label('Réponse(s)')->badge()->color('info')->separator(','),
                        ])
                        ->columns(2)
                        ->placeholder('Aucune réponse enregistrée'),
                ]),

            Section::make('Adresse')
                ->icon('heroicon-o-map-pin')
                ->collapsible()
                ->schema([
                    TextEntry::make('rue')->label('Rue')->placeholder('—'),
                    TextEntry::make('ville')->label('Ville')->placeholder('—'),
                    TextEntry::make('code_postal')->label('Code postal')->placeholder('—'),
                ])->columns(3),

            Section::make('Appareil')
                ->icon('heroicon-o-device-phone-mobile')
                ->collapsible()
                ->schema([
                    TextEntry::make('devices.deviceModel')->label('Modèle')->placeholder('—'),
                    TextEntry::make('devices.deviceBrand')->label('Marque')->placeholder('—'),
                    TextEntry::make('devices.deviceOsVersion')->label('OS')->placeholder('—'),
                    TextEntry::make('devices.appVersion')->label('Version App')->placeholder('—'),
                ])->columns(4),


            Section::make('Documents d\'identité')
                ->icon('heroicon-o-identification')
                ->collapsible()
                ->schema([
                    ImageEntry::make('piece_d_identite')
                        ->label('Pièce d\'identité (recto)')->disk('s3')->height(200)->placeholder('—'),
                    ImageEntry::make('piece_d_identite_verso')
                        ->label('Pièce d\'identité (verso)')->disk('s3')->height(200)->placeholder('—'),
                    ImageEntry::make('KBIS_file')
                        ->label('KBIS')->disk('s3')->height(200)->placeholder('—'),
                ])->columns(3),

            Section::make('Autres documents')
                ->icon('heroicon-o-paper-clip')
                ->collapsible()
                ->collapsed()
                ->hidden(fn ($record) => $record->otherDocuments->isEmpty())
                ->schema([
                    RepeatableEntry::make('otherDocuments')
                        ->label('')
                        ->schema([
                            TextEntry::make('document_title')->label('Titre')->placeholder('—'),
                            ImageEntry::make('document_path')->label('Document')->disk('s3')->height(180),
                        ])->columns(2),
                ]),

            Section::make('Payouts effectués')
                ->icon('heroicon-o-banknotes')
                ->collapsible()
                ->schema([
                    RepeatableEntry::make('guide_payouts')
                        ->label('')
                        ->getStateUsing(fn ($record) => $record->Guide->first()?->payouts->sortByDesc('paid_at')->map->toArray()->toArray() ?? [])
                        ->schema([
                            TextEntry::make('payment_period')->label('Période'),
                            TextEntry::make('amount')->label('Montant')
                                ->formatStateUsing(fn ($state) => number_format($state, 2, ',', ' ') . ' €')
                                ->color('success'),
                            TextEntry::make('paid_at')->label('Date')
                                ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y') : '—'),
                            TextEntry::make('stripe_transfer_id')->label('ID Stripe')->placeholder('—'),
                            TextEntry::make('invoice_url')
                                ->label('Facture')
                                ->formatStateUsing(fn ($state) => $state ? 'Télécharger' : '—')
                                ->url(fn ($state) => $state ?: null)
                                ->openUrlInNewTab()
                                ->color(fn ($state) => $state ? 'primary' : 'gray'),
                        ])->columns(5)
                        ->placeholder('Aucun virement'),
                ]),

            Section::make('Virements échoués')
                ->icon('heroicon-o-exclamation-circle')
                ->collapsible()
                ->hidden(fn ($record) => $record->Guide->first()?->failedPayouts->isEmpty() ?? true)
                ->schema([
                    RepeatableEntry::make('guide_failed_payouts')
                        ->label('')
                        ->getStateUsing(fn ($record) => $record->Guide->first()?->failedPayouts->map->toArray()->toArray() ?? [])
                        ->schema([
                            TextEntry::make('month')->label('Mois'),
                            TextEntry::make('payout_amount')->label('Montant')
                                ->formatStateUsing(fn ($state) => number_format($state, 2, ',', ' ') . ' €'),
                            TextEntry::make('failure_message')->label('Erreur')->color('danger')->placeholder('—'),
                            TextEntry::make('status')->label('Statut')->badge()
                                ->color(fn ($state) => $state === 'resolved' ? 'success' : 'danger'),
                        ])->columns(4),
                ]),

        ]);
    }
}
