<?php

namespace App\Filament\Resources;

use App\Enums\TrackingAction;
use App\Filament\Resources\UserTrackingArchiveResource\Pages;
use App\Models\UserTrackingArchive;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserTrackingArchiveResource extends Resource
{
    protected static ?string $model = UserTrackingArchive::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Archive des actions';
    protected static ?string $navigationGroup = 'Suivi';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'action archivée';
    protected static ?string $pluralModelLabel = 'archive des actions';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Détail de l\'action archivée')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('user.name')->label('Utilisateur')->placeholder('—'),
                    TextEntry::make('user.email')->label('Email')->copyable()->placeholder('—'),
                    TextEntry::make('action_label')
                        ->label('Action')
                        ->state(fn ($record) => $record->getActionLabelAttribute())
                        ->badge()
                        ->color(fn ($record) => $record->getActionColorAttribute()),
                    TextEntry::make('actor_type')->label('Type d\'acteur')->placeholder('—'),
                    TextEntry::make('subject_type')->label('Sujet')->placeholder('—'),
                    TextEntry::make('method')->label('Méthode HTTP')->placeholder('—'),
                    TextEntry::make('route')->label('Route')->placeholder('—')->columnSpanFull(),
                    TextEntry::make('ip_address')->label('Adresse IP')->placeholder('—'),
                    TextEntry::make('created_at')->label('Date archivée')->dateTime('d/m/Y H:i:s')->placeholder('—'),
                ]),
            ]),

            Section::make('Métadonnées')
                ->schema([
                    TextEntry::make('metadata')
                        ->label('')
                        ->state(fn ($record) => $record->metadata
                            ? json_encode($record->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                            : '—'
                        )
                        ->columnSpanFull()
                        ->fontFamily('mono'),
                ])
                ->visible(fn ($record) => !empty($record->metadata)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                UserTrackingArchive::with('user')
                    ->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->formatStateUsing(fn ($state) => TrackingAction::tryFrom($state)?->label() ?? $state)
                    ->color(fn ($state) => TrackingAction::tryFrom($state)?->color() ?? 'gray')
                    ->searchable(),

                TextColumn::make('actor_type')
                    ->label('Acteur')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('method')
                    ->label('Méthode')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'POST'        => 'success',
                        'PUT', 'PATCH' => 'warning',
                        'DELETE'      => 'danger',
                        default       => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Archivé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Type d\'action')
                    ->options(
                        collect(TrackingAction::cases())
                            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
                            ->toArray()
                    ),

                SelectFilter::make('actor_type')
                    ->label('Acteur')
                    ->options([
                        'guide'    => 'Guide',
                        'voyageur' => 'Voyageur',
                        'admin'    => 'Admin',
                    ]),

                Filter::make('periode')
                    ->label('Période')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('date_from')->label('Du'),
                        \Filament\Forms\Components\DatePicker::make('date_to')->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $query
                            ->when($data['date_from'] ?? null, fn ($q) => $q->whereDate('created_at', '>=', $data['date_from']))
                            ->when($data['date_to'] ?? null, fn ($q) => $q->whereDate('created_at', '<=', $data['date_to']));
                    }),
            ])
            ->actions([
                ViewAction::make()->label('Détail'),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserTrackingArchives::route('/'),
            'view'  => Pages\ViewUserTrackingArchive::route('/{record}'),
        ];
    }
}
