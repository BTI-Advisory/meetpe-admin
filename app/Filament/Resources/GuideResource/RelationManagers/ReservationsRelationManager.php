<?php

namespace App\Filament\Resources\GuideResource\RelationManagers;

use App\Models\Reservation;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'experiences';
    protected static ?string $title = 'Réservations';
    protected static ?string $icon = 'heroicon-o-calendar-days';

    public function table(Table $table): Table
    {
        $userId = $this->getOwnerRecord()->id;

        return $table
            ->query(
                Reservation::query()
                    ->join('guide_experiences', 'reservations.experience_id', '=', 'guide_experiences.id')
                    ->join('users as voyageurs', 'reservations.voyageur_id', '=', 'voyageurs.id')
                    ->where('guide_experiences.user_id', $userId)
                    ->select(
                        'reservations.*',
                        'guide_experiences.title as experience_title',
                        'voyageurs.name as voyageur_name',
                    )
            )
            ->columns([
                TextColumn::make('experience_title')
                    ->label('Expérience')
                    ->searchable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),

                TextColumn::make('voyageur_name')
                    ->label('Voyageur')
                    ->searchable(),

                TextColumn::make('date_time')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('nombre_des_voyageurs')
                    ->label('Voyageurs')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'Acceptée'    => 'success',
                        'En attente'  => 'warning',
                        'Annulée', 'Refusée' => 'danger',
                        default       => 'gray',
                    }),

                TextColumn::make('is_payed')
                    ->label('Payé')
                    ->badge()
                    ->state(fn ($state) => $state ? 'Oui' : 'Non')
                    ->color(fn ($state) => $state === 'Oui' ? 'success' : 'gray'),

                TextColumn::make('total_price')
                    ->label('Montant')
                    ->state(fn ($state) => number_format($state, 2, ',', ' ') . ' €')
                    ->sortable(),
            ])
            ->defaultSort('reservations.created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
