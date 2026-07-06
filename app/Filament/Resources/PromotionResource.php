<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionResource\Pages;
use App\Models\Promotion;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Cache;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;
    protected static ?string $navigationIcon  = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Promotions';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int    $navigationSort  = 1;
    protected static ?string $modelLabel         = 'promotion';
    protected static ?string $pluralModelLabel   = 'promotions';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('label')
                ->label('Nom de la promotion')
                ->required()
                ->maxLength(255)
                ->placeholder('Ex : Été 2026'),

            TextInput::make('discount_percentage')
                ->label('Réduction (%)')
                ->required()
                ->numeric()
                ->minValue(1)
                ->maxValue(100)
                ->suffix('%')
                ->placeholder('Ex : 15'),

            DateTimePicker::make('starts_at')
                ->label('Date de début')
                ->nullable()
                ->displayFormat('d/m/Y H:i'),

            DateTimePicker::make('ends_at')
                ->label('Date de fin')
                ->nullable()
                ->displayFormat('d/m/Y H:i')
                ->after('starts_at'),

            Toggle::make('is_active')
                ->label('Activer immédiatement')
                ->helperText('Désactive automatiquement toute autre promotion active.')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('discount_percentage')
                    ->label('Réduction')
                    ->suffix('%')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->label('Début')
                    ->dateTime('d/m/Y')
                    ->placeholder('—'),

                TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y')
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->getStateUsing(function (Promotion $record): string {
                        if ($record->is_active) return 'Actif';
                        if ($record->ends_at && $record->ends_at->isPast()) return 'Expiré';
                        if ($record->starts_at && $record->starts_at->isFuture()) return 'Planifié';
                        return 'Inactif';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Actif'    => 'success',
                        'Expiré'   => 'gray',
                        'Planifié' => 'info',
                        default    => 'warning',
                    }),
            ])
            ->actions([
                Action::make('toggle')
                    ->label(fn (Promotion $record) => $record->is_active ? 'Désactiver' : 'Activer')
                    ->icon(fn (Promotion $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Promotion $record) => $record->is_active ? 'warning' : 'success')
                    ->action(function (Promotion $record) {
                        if (!$record->is_active) {
                            Promotion::where('id', '!=', $record->id)->update(['is_active' => false]);
                        }
                        $record->update(['is_active' => !$record->is_active]);
                        self::clearCache();

                        $label = $record->is_active ? 'activée' : 'désactivée';
                        Notification::make()
                            ->title("Promotion « {$record->label} » $label.")
                            ->success()
                            ->send();
                    }),

                EditAction::make()
                    ->after(fn () => self::clearCache()),

                DeleteAction::make()
                    ->after(fn () => self::clearCache()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nouvelle promotion')
                    ->after(fn () => self::clearCache()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function clearCache(): void
    {
        Cache::forget('active_promotion');
        Cache::forget('all_experiences_online');
        Cache::forget('all_experiences_online_gz');
        self::flushMatchingCache();
    }

    private static function flushMatchingCache(): void
    {
        $store = Cache::getStore();

        if (!($store instanceof \Illuminate\Cache\RedisStore)) {
            return;
        }

        $redis       = $store->connection();
        $cachePrefix = $store->getPrefix();

        $fullKeys = $redis->keys($cachePrefix . 'matching_response_*');

        if (empty($fullKeys)) {
            return;
        }

        $redisPrefix  = config('database.redis.options.prefix', '');
        $keysToDelete = array_map(fn($k) => substr($k, strlen($redisPrefix)), $fullKeys);
        $redis->del($keysToDelete);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromotions::route('/'),
        ];
    }
}
