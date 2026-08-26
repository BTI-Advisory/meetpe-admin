<?php

namespace App\Filament\Resources;

use App\Enums\ReservationStatus;
use App\Filament\Resources\ReservationIncompleteResource\Pages;
use App\Models\Reservation;
use App\Models\Voyageur;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ReservationIncompleteResource extends Resource
{
    protected static ?string $model = Reservation::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Réservations incomplètes';
    protected static ?string $navigationGroup = 'Réservations';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $modelLabel         = 'réservation incomplète';
    protected static ?string $pluralModelLabel   = 'réservations incomplètes';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Reservation::with(['experience', 'experience.photoprincipal', 'voyageur'])
                    ->where('status', ReservationStatus::CREATED->value)
                    ->latest('created_at')
            )
            ->columns([
                // ── Voyageur ─────────────────────────────────────────────────
                ImageColumn::make('voyageur_photo')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(asset('img/logo-ct-dark.png'))
                    ->getStateUsing(fn ($record) => $record->voyageur?->profile_path
                        ? Storage::disk('s3')->url($record->voyageur->profile_path)
                        : null)
                    ->width(44)
                    ->height(44),

                TextColumn::make('voyageur_info')
                    ->label('Voyageur')
                    ->state(fn ($record) => $record->voyageur?->name ?? $record->nom ?? '—')
                    ->description(fn ($record) => $record->voyageur?->email ?? '—')
                    ->url(fn ($record) => $record->voyageur_id
                        ? VoyageurResource::getUrl('view', [
                            'record' => Voyageur::where('user_id', $record->voyageur_id)->value('voyageur_id'),
                          ])
                        : null)
                    ->searchable(query: fn ($query, $search) => $query
                        ->where('nom', 'like', "%{$search}%")
                        ->orWhereHas('voyageur', fn ($q) => $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")))
                    ->openUrlInNewTab(),

                TextColumn::make('voyageur_phone')
                    ->label('Téléphone')
                    ->state(fn ($record) => $record->voyageur?->phone_number ?? $record->phone ?? '—')
                    ->placeholder('—')
                    ->icon('heroicon-m-phone'),

                // ── Expérience ────────────────────────────────────────────────
                ImageColumn::make('experience.photoprincipal.photo_url')
                    ->label('')
                    ->defaultImageUrl(asset('img/logo-ct-dark.png'))
                    ->width(44)
                    ->height(44)
                    ->extraImgAttributes(['style' => 'border-radius:6px']),

                TextColumn::make('experience.title')
                    ->label('Expérience')
                    ->limit(30)
                    ->description(fn ($record) => $record->experience?->ville ?? '')
                    ->searchable()
                    ->url(fn ($record) => $record->experience_id
                        ? GuideExperienceResource::getUrl('view', ['record' => $record->experience_id])
                        : null)
                    ->openUrlInNewTab(),

                // ── Réservation ───────────────────────────────────────────────
                TextColumn::make('created_at')
                    ->label('Réservé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn ($record) => $record->nombre_des_voyageurs . ' voyageur(s)'),

                TextColumn::make('date_time')
                    ->label('Créneau')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->badge()
                    ->color('info'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([])
            ->bulkActions([])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservationsIncompletes::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
