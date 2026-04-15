<?php

namespace App\Filament\Resources;

use App\Exports\VoyageursExport;
use App\Filament\Resources\VoyageurResource\Pages;
use App\Filament\Resources\VoyageurResource\RelationManagers\ReservationsRelationManager;
use App\Models\Voyageur;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class VoyageurResource extends Resource
{
    protected static ?string $model = Voyageur::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Voyageurs';
    protected static ?string $navigationGroup = 'Utilisateurs';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'voyageur';
    protected static ?string $pluralModelLabel = 'voyageurs';

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'user.email'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Email' => $record->user?->email,
            'Ville' => $record->ville ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Informations')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('user.name')->label('Nom'),
                    TextEntry::make('user.email')->label('Email')->copyable(),
                    TextEntry::make('user.phone_number')->label('Téléphone'),
                    TextEntry::make('user.age')->label('Âge')->suffix(' ans'),
                    TextEntry::make('ville')->label('Ville')->placeholder('—'),
                    TextEntry::make('pays')->label('Pays')->placeholder('—'),
                ]),
            ]),
            Section::make('Compte')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('account_status')
                        ->label('Statut')
                        ->state(fn (Voyageur $record) => $record->user?->is_verified_account ? 'Actif' : 'Inactif')
                        ->badge()
                        ->color(fn ($state) => $state === 'Actif' ? 'success' : 'danger'),
                    TextEntry::make('user.created_at')->label('Inscrit le')->date('d/m/Y'),
                ]),
            ]),
            Section::make('Voyage')->schema([
                Grid::make(3)->schema([
                    TextEntry::make('date_arrivee')->label('Arrivée')->date('d/m/Y'),
                    TextEntry::make('date_depart')->label('Départ')->date('d/m/Y'),
                    TextEntry::make('reservations_count')
                        ->label('Réservations')
                        ->state(fn (Voyageur $record) => $record->reservations()->count()),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Voyageur::with('user')->orderByDesc('created_at'))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('user.phone_number')
                    ->label('Téléphone'),

                TextColumn::make('user.age')
                    ->label('Âge')
                    ->suffix(' ans'),

                TextColumn::make('ville')
                    ->label('Ville')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('account_status')
                    ->label('Statut')
                    ->badge()
                    ->state(fn (Voyageur $record) => $record->user?->is_verified_account ? 'Actif' : 'Inactif')
                    ->color(fn ($state) => $state === 'Actif' ? 'success' : 'danger'),

                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('reservations_count')
                    ->label('Réservations')
                    ->counts('reservations')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Filter::make('date_inscription')
                    ->label('Date d\'inscription')
                    ->form([
                        DatePicker::make('from')->label('Du'),
                        DatePicker::make('until')->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $query
                            ->when($data['from'], fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])
            ->actions([
ViewAction::make()->label('Détail'),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Exporter')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn () => Excel::download(new VoyageursExport(), 'voyageurs.xlsx')),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelationManagers(): array
    {
        return [
            ReservationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVoyageurs::route('/'),
            'view'  => Pages\ViewVoyageur::route('/{record}'),
        ];
    }
}
