<?php

namespace App\Filament\Resources;

use App\Enums\ReservationStatus;
use App\Exports\ReservationsExport;
use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Réservations';
    protected static ?string $navigationGroup = 'Réservations';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'réservation';
    protected static ?string $pluralModelLabel = 'réservations';

    public static function getGloballySearchableAttributes(): array
    {
        return ['nom', 'experience.title', 'voyageur.name', 'voyageur.email'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Voyageur' => $record->voyageur?->name ?? $record->nom,
            'Statut'   => $record->status,
            'Montant'  => $record->total_price ? number_format($record->total_price / 100, 2) . ' €' : '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Expérience')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('experience.title')->label('Titre'),
                    TextEntry::make('experience.user.name')->label('Guide'),
                    TextEntry::make('experience.ville')->label('Ville'),
                    TextEntry::make('experience.prix_par_voyageur')->label('Prix / voyageur')->money('EUR'),
                ]),
            ]),

            Section::make('Réservation')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('voyageur.name')->label('Nom voyageur')
                        ->state(fn ($record) => $record->voyageur?->name ?? $record->nom)
                        ->placeholder('—'),
                    TextEntry::make('phone')->label('Téléphone')
                        ->state(fn ($record) => $record->voyageur?->phone_number ?? $record->phone)
                        ->placeholder('—'),
                    TextEntry::make('date_time')->label('Date')->dateTime('d/m/Y H:i'),
                    TextEntry::make('nombre_des_voyageurs')->label('Nb. voyageurs'),
                    TextEntry::make('is_group')->label('Groupe privé')
                        ->state(fn ($record) => $record->is_group ? 'Oui' : 'Non'),
                    TextEntry::make('message_au_guide')->label('Message au guide')
                        ->placeholder('—')
                        ->columnSpanFull(),
                ]),
            ]),

            Section::make('Paiement')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('status')->label('Statut')->badge()
                        ->color(fn (string $state): string => match ($state) {
                            ReservationStatus::ACCEPTÉE->value  => 'success',
                            ReservationStatus::REFUSÉE->value,
                            ReservationStatus::ANNULÉE->value   => 'danger',
                            ReservationStatus::ARCHIVÉE->value  => 'gray',
                            ReservationStatus::PENDING->value   => 'warning',
                            default                             => 'gray',
                        }),
                    TextEntry::make('is_payed')->label('Payée')
                        ->state(fn ($record) => $record->is_payed ? 'Oui' : 'Non'),
                    TextEntry::make('total_price')->label('Total')->money('EUR')->placeholder('—'),
                    TextEntry::make('guide_payout_amount')->label('Reversement guide')->money('EUR')->placeholder('—'),
                    TextEntry::make('commission_meetpe')->label('Commission MeetPe')->money('EUR')->placeholder('—'),
                    TextEntry::make('stripe_payment_intent_id')->label('Payment Intent')->placeholder('—')->copyable(),
                ]),
            ]),

            Section::make('Annulation')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('canceled_at')->label('Annulée le')->dateTime('d/m/Y H:i'),
                        TextEntry::make('cancel_reason')->label('Raison')->placeholder('—'),
                        TextEntry::make('cancel_description')->label('Description')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('refund_amount')->label('Remboursement')->money('EUR')->placeholder('—'),
                        TextEntry::make('stripe_refund_status')->label('Statut remboursement')->placeholder('—'),
                    ]),
                ])
                ->visible(fn ($record) => !empty($record->canceled_at)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Reservation::with(['experience', 'experience.photoprincipal', 'experience.user', 'voyageur'])
                    ->whereNotIn('status', [ReservationStatus::CREATED->value])
            )
            ->columns([
                ImageColumn::make('experience.photoprincipal.photo_url')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(asset('img/logo-ct-dark.png'))
                    ->width(48)
                    ->height(48),

                TextColumn::make('experience.title')
                    ->label('Expérience')
                    ->searchable()
                    ->sortable()
                    ->limit(35),

                TextColumn::make('experience.user.name')
                    ->label('Guide')
                    ->searchable(),

                TextColumn::make('voyageur_name')
                    ->label('Voyageur')
                    ->state(fn ($record) => $record->voyageur?->name ?? $record->nom)
                    ->searchable(query: fn ($query, $search) => $query
                        ->whereHas('voyageur', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhere('nom', 'like', "%{$search}%")
                    )
                    ->sortable(query: fn ($query, $direction) => $query
                        ->leftJoin('users as vuser', 'reservations.voyageur_id', '=', 'vuser.id')
                        ->orderBy('vuser.name', $direction)
                    )
                    ->placeholder('—'),

                TextColumn::make('experience.ville')
                    ->label('Ville')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('date_time')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('nombre_des_voyageurs')
                    ->label('Nb.')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('total_price')
                    ->label('Montant')
                    ->money('EUR')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        ReservationStatus::ACCEPTÉE->value  => 'success',
                        ReservationStatus::REFUSÉE->value,
                        ReservationStatus::ANNULÉE->value   => 'danger',
                        ReservationStatus::ARCHIVÉE->value  => 'gray',
                        ReservationStatus::PENDING->value   => 'warning',
                        default                             => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(collect(ReservationStatus::cases())
                        ->mapWithKeys(fn ($e) => [$e->value => $e->value])
                        ->toArray()
                    ),

                Filter::make('date_reservation')
                    ->label('Date de réservation')
                    ->form([
                        DatePicker::make('from')->label('Du'),
                        DatePicker::make('until')->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $query
                            ->when($data['from'], fn ($q) => $q->whereDate('date_time', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('date_time', '<=', $data['until']));
                    }),

                Filter::make('guide')
                    ->label('Guide')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('guide_name')->label('Nom du guide'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['guide_name'])) {
                            $query->whereHas('experience.user', fn ($q) => $q->where('name', 'like', '%' . $data['guide_name'] . '%'));
                        }
                    }),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Exporter')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn () => Excel::download(new ReservationsExport(), 'reservations.xlsx')),
            ])
            ->actions([
                Action::make('annuler')
                    ->label('Annuler')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Annuler cette réservation ?')
                    ->form([
                        Textarea::make('cancel_reason')
                            ->label('Motif d\'annulation')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(fn (Reservation $record) => in_array($record->status, [
                        ReservationStatus::ACCEPTÉE->value,
                        ReservationStatus::PENDING->value,
                    ]))
                    ->action(function (Reservation $record, array $data) {
                        $record->status        = ReservationStatus::ANNULÉE->value;
                        $record->canceled_at   = now();
                        $record->cancel_reason = $data['cancel_reason'];
                        $record->save();
                        Log::info('DASHBOARD_ACTION : Réservation #' . $record->id . ' annulée par admin');
                        Notification::make()->title('Réservation annulée')->success()->send();
                    }),

                Action::make('contacter')
                    ->label('Contacter')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->form([
                        Select::make('destinataire')
                            ->label('Destinataire')
                            ->options(['voyageur' => 'Voyageur', 'guide' => 'Guide'])
                            ->required(),
                        Textarea::make('message')
                            ->label('Message')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (Reservation $record, array $data) {
                        $email = null;
                        $name  = null;

                        if ($data['destinataire'] === 'voyageur') {
                            $email = $record->voyageur?->email;
                            $name  = $record->voyageur?->name ?? $record->nom;
                        } else {
                            $email = $record->experience?->user?->email;
                            $name  = $record->experience?->user?->name;
                        }

                        if ($email) {
                            Mail::raw($data['message'], function ($msg) use ($email, $name) {
                                $msg->to($email, $name)
                                    ->subject('Message de l\'équipe MeetPe');
                            });
                            Log::info('DASHBOARD_ACTION : Message envoyé à ' . $email);
                            Notification::make()->title('Message envoyé à ' . $name)->success()->send();
                        } else {
                            Notification::make()->title('Email introuvable')->danger()->send();
                        }
                    }),

                ViewAction::make()->label('Détail'),
            ])
            ->bulkActions([])
            ->defaultSort('date_time', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'view'  => Pages\ViewReservation::route('/{record}'),
        ];
    }
}
