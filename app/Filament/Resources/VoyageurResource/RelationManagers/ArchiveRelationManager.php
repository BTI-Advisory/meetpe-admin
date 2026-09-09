<?php

namespace App\Filament\Resources\VoyageurResource\RelationManagers;

use App\Models\UserTrackingArchive;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArchiveRelationManager extends RelationManager
{
    protected static string $relationship = 'userTrackingArchives';
    protected static ?string $title = 'Archive des actions';
    protected static ?string $icon = 'heroicon-o-archive-box';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => UserTrackingArchive::where('user_id', $this->getOwnerRecord()->user_id)->latest())
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->state(fn (UserTrackingArchive $record) => $record->action_label)
                    ->color(fn (UserTrackingArchive $record) => $record->action_color),

                TextColumn::make('actor_type')
                    ->label('Type')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('route')
                    ->label('Route')
                    ->placeholder('—'),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100])
            ->emptyStateHeading('Aucune action archivée');
    }
}
