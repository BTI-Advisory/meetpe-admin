<?php

namespace App\Filament\Resources\VoyageurResource\RelationManagers;

use App\Enums\ReservationStatus;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'reservations';
    protected static ?string $title = 'Historique des réservations';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('experience.title')
                    ->label('Expérience')
                    ->limit(30),

                TextColumn::make('experience.ville')
                    ->label('Ville')
                    ->placeholder('—'),

                TextColumn::make('date_time')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('nombre_des_voyageurs')
                    ->label('Nb.')
                    ->alignCenter(),

                TextColumn::make('total_price')
                    ->label('Montant')
                    ->money('EUR')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        ReservationStatus::ACCEPTÉE->value  => 'success',
                        ReservationStatus::REFUSÉE->value,
                        ReservationStatus::ANNULÉE->value   => 'danger',
                        ReservationStatus::ARCHIVÉE->value  => 'gray',
                        ReservationStatus::PENDING->value   => 'warning',
                        default                             => 'gray',
                    }),
            ])
            ->defaultSort('date_time', 'desc');
    }
}
