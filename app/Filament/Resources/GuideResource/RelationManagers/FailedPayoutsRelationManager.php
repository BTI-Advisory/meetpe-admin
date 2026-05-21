<?php

namespace App\Filament\Resources\GuideResource\RelationManagers;

use App\Models\FailedPayout;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FailedPayoutsRelationManager extends RelationManager
{
    protected static string $relationship = 'Guide';
    protected static ?string $title = 'Virements échoués';
    protected static ?string $icon = 'heroicon-o-exclamation-triangle';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => FailedPayout::where('guide_id', optional($this->getOwnerRecord()->Guide->first())->guide_id)->orderByDesc('created_at'))
            ->heading('Virements échoués')
            ->columns([
                TextColumn::make('month')
                    ->label('Mois')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('payout_amount')
                    ->label('Montant')
                    ->state(fn (FailedPayout $record) => $record->payout_amount
                        ? number_format($record->payout_amount, 2, ',', ' ') . ' €'
                        : '—')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (?string $state) => $state === 'resolved' ? 'success' : 'danger'),

                TextColumn::make('stripe_account_id')
                    ->label('Compte Stripe')
                    ->copyable()
                    ->placeholder('—'),

                TextColumn::make('failure_message')
                    ->label("Raison")
                    ->wrap()
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->paginated([10, 25])
            ->emptyStateHeading('Aucun virement échoué');
    }
}
