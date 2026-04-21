<?php

namespace App\Filament\Resources\GuideResource\RelationManagers;

use App\Models\FailedPayout;
use App\Models\Payout;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayoutsRelationManager extends RelationManager
{
    protected static string $relationship = 'Guide';
    protected static ?string $title = 'Payouts';
    protected static ?string $icon = 'heroicon-o-banknotes';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Payout::where('guide_id', optional($this->getOwnerRecord()->Guide->first())->guide_id)->orderByDesc('paid_at'))
            ->heading('Virements')
            ->columns([
                TextColumn::make('payment_period')
                    ->label('Période')
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->state(fn (Payout $record) => number_format($record->amount, 2, ',', ' ') . ' €')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->color('success')
                    ->sortable(),

                TextColumn::make('paid_at')
                    ->label('Payé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('stripe_transfer_id')
                    ->label('ID Stripe')
                    ->copyable()
                    ->placeholder('—'),

                TextColumn::make('invoice_url')
                    ->label('Facture')
                    ->url(fn (Payout $record) => $record->invoice_url)
                    ->openUrlInNewTab()
                    ->placeholder('—'),
            ])
            ->paginated([10, 25, 50])
            ->emptyStateHeading('Aucun virement');
    }
}
