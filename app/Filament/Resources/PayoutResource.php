<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayoutResource\Pages;
use App\Models\Payout;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PayoutResource extends Resource
{
    protected static ?string $model = Payout::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Virements';
    protected static ?string $navigationGroup = 'Réservations';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'virement';
    protected static ?string $pluralModelLabel = 'virements';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Détail du virement')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('guide.user.name')->label('Guide')->placeholder('—'),
                    TextEntry::make('guide.user.email')->label('Email')->copyable()->placeholder('—'),
                    TextEntry::make('amount')
                        ->label('Montant')
                        ->state(fn (Payout $record) => number_format($record->amount / 100, 2, ',', ' ') . ' €'),
                    TextEntry::make('payment_period')->label('Période')->placeholder('—'),
                    TextEntry::make('stripe_transfer_id')->label('Stripe Transfer ID')->copyable()->placeholder('—'),
                    TextEntry::make('paid_at')->label('Payé le')->dateTime('d/m/Y H:i')->placeholder('—'),
                    TextEntry::make('invoice_url')
                        ->label('Facture')
                        ->url(fn ($record) => $record->invoice_url)
                        ->openUrlInNewTab()
                        ->placeholder('—'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Payout::with(['guide', 'guide.user'])->orderByDesc('paid_at'))
            ->columns([
                TextColumn::make('guide.user.name')
                    ->label('Guide')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('guide.user.email')
                    ->label('Email')
                    ->copyable()
                    ->placeholder('—'),

                TextColumn::make('amount')
                    ->label('Montant')
                    ->state(fn (Payout $record) => number_format($record->amount / 100, 2, ',', ' ') . ' €')
                    ->sortable(),

                TextColumn::make('payment_period')
                    ->label('Période')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('stripe_transfer_id')
                    ->label('Stripe ID')
                    ->copyable()
                    ->limit(20)
                    ->placeholder('—'),

                TextColumn::make('paid_at')
                    ->label('Payé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),
            ])
            ->filters([
                Filter::make('periode')
                    ->label('Période de paiement')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('payment_period')->label('Période (ex: 2024-01)'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['payment_period'])) {
                            $query->where('payment_period', 'like', '%' . $data['payment_period'] . '%');
                        }
                    }),

                Filter::make('guide')
                    ->label('Guide')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('guide_name')->label('Nom du guide'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['guide_name'])) {
                            $query->whereHas('guide.user', fn ($q) => $q->where('name', 'like', '%' . $data['guide_name'] . '%'));
                        }
                    }),
            ])
            ->actions([
                ViewAction::make()->label('Détail'),
            ])
            ->bulkActions([])
            ->defaultSort('paid_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayouts::route('/'),
            'view'  => Pages\ViewPayout::route('/{record}'),
        ];
    }
}
