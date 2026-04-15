<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AvisResource\Pages;
use App\Models\Avis;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AvisResource extends Resource
{
    protected static ?string $model = Avis::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Avis';
    protected static ?string $navigationGroup = 'Expériences';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'avis';
    protected static ?string $pluralModelLabel = 'avis';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Détail de l\'avis')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('user.name')->label('Voyageur'),
                    TextEntry::make('user.email')->label('Email')->copyable(),
                    TextEntry::make('experience.title')->label('Expérience')->placeholder('—'),
                    TextEntry::make('experience.user.name')->label('Guide')->placeholder('—'),
                    TextEntry::make('note')->label('Note')->suffix(' / 5'),
                    TextEntry::make('created_at')->label('Date')->dateTime('d/m/Y H:i'),
                    TextEntry::make('message')->label('Commentaire')->columnSpanFull()->placeholder('—'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Avis::with(['user', 'experience', 'experience.user'])->orderByDesc('created_at'))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Voyageur')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('experience.title')
                    ->label('Expérience')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->placeholder('—'),

                TextColumn::make('experience.user.name')
                    ->label('Guide')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('note')
                    ->label('Note')
                    ->badge()
                    ->suffix(' ★')
                    ->alignCenter()
                    ->color(fn ($state) => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default     => 'danger',
                    }),

                TextColumn::make('message')
                    ->label('Commentaire')
                    ->limit(50)
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('note')
                    ->label('Note')
                    ->options([
                        '5' => '5 ★',
                        '4' => '4 ★',
                        '3' => '3 ★',
                        '2' => '2 ★',
                        '1' => '1 ★',
                    ]),

                Filter::make('guide')
                    ->label('Guide')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('guide_name')->label('Nom du guide'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['guide_name'])) {
                            $query->whereHas('experience.user', fn ($q) => $q->where('name', 'like', '%' . $data['guide_name'] . '%'));
                        }
                    }),

                Filter::make('experience')
                    ->label('Expérience')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('experience_title')->label('Titre de l\'expérience'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['experience_title'])) {
                            $query->whereHas('experience', fn ($q) => $q->where('title', 'like', '%' . $data['experience_title'] . '%'));
                        }
                    }),
            ])
            ->actions([
                ViewAction::make()->label('Détail'),
                DeleteAction::make()->label('Supprimer'),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAvis::route('/'),
            'view'  => Pages\ViewAvis::route('/{record}'),
        ];
    }
}
