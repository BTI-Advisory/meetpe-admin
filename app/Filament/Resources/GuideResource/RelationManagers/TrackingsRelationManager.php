<?php

namespace App\Filament\Resources\GuideResource\RelationManagers;

use App\Models\UserTracking;
use Carbon\Carbon;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrackingsRelationManager extends RelationManager
{
    protected static string $relationship = 'userTrackings';
    protected static ?string $title = 'Historique (3 mois)';
    protected static ?string $icon = 'heroicon-o-clock';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => UserTracking::where('user_id', $this->getOwnerRecord()->id)
                ->where('created_at', '>=', Carbon::now()->subMonths(3))
                ->latest()
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->state(fn (UserTracking $record) => $record->action_label)
                    ->color(fn (UserTracking $record) => $record->action_color),

                TextColumn::make('route')
                    ->label('Route')
                    ->placeholder('—'),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100])
            ->emptyStateHeading('Aucune action enregistrée');
    }
}
