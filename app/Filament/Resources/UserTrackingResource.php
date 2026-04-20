<?php

namespace App\Filament\Resources;

use App\Enums\TrackingAction;
use App\Filament\Resources\UserTrackingResource\Pages;
use App\Models\UserTracking;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class UserTrackingResource extends Resource
{
    protected static ?string $model = UserTracking::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Historique des actions';
    protected static ?string $navigationGroup = 'Suivi';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'action';
    protected static ?string $pluralModelLabel = 'historique des actions';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Détail de l\'action')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('user.name')->label('Utilisateur')->placeholder('—'),
                    TextEntry::make('user.email')->label('Email')->copyable()->placeholder('—'),
                    TextEntry::make('action_label')
                        ->label('Action')
                        ->badge()
                        ->color(fn ($record) => $record->action_color),
                    TextEntry::make('actor_type')->label('Type d\'acteur')->placeholder('—'),
                    TextEntry::make('subject_type')->label('Sujet')->placeholder('—'),
                    TextEntry::make('method')->label('Méthode HTTP')->placeholder('—'),
                    TextEntry::make('route')->label('Route')->placeholder('—')->columnSpanFull(),
                    TextEntry::make('ip_address')->label('Adresse IP')->placeholder('—'),
                    TextEntry::make('timestamp')->label('Date')->dateTime('d/m/Y H:i:s')->placeholder('—'),
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
                UserTracking::with('user')
                    ->where('created_at', '>=', Carbon::now()->subMonths(3))
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

                TextColumn::make('action_label')
                    ->label('Action')
                    ->badge()
                    ->color(fn ($record) => $record->action_color)
                    ->searchable(query: fn (Builder $q, string $s) => $q->where('action', 'like', "%$s%")),

                TextColumn::make('actor_type')
                    ->label('Acteur')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('subject_type')
                    ->label('Sujet')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('method')
                    ->label('Méthode')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'POST'   => 'success',
                        'PUT', 'PATCH' => 'warning',
                        'DELETE' => 'danger',
                        default  => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Date')
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

                Filter::make('utilisateur')
                    ->label('Utilisateur')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('name')->label('Nom'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['name'])) {
                            $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%' . $data['name'] . '%'));
                        }
                    }),

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
            'index' => Pages\ListUserTrackings::route('/'),
            'view'  => Pages\ViewUserTracking::route('/{record}'),
        ];
    }
}
