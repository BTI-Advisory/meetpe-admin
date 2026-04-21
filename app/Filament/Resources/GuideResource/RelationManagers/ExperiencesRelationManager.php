<?php

namespace App\Filament\Resources\GuideResource\RelationManagers;

use App\Enums\GuideExperienceStatusEnum;
use App\Filament\Resources\GuideExperienceResource;
use App\Models\GuideExperience;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExperiencesRelationManager extends RelationManager
{
    protected static string $relationship = 'experiences';
    protected static ?string $title = 'Expériences';
    protected static ?string $icon = 'heroicon-o-map';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('photoprincipal')->latest())
            ->columns([
                ImageColumn::make('photoprincipal.photo_url')
                    ->label('')
                    ->height(48)
                    ->width(48)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover']),

                TextColumn::make('title')
                    ->label('Titre')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->searchable()
                    ->url(fn (GuideExperience $record) => GuideExperienceResource::getUrl('view', ['record' => $record->id]))
                    ->color('primary'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        GuideExperienceStatusEnum::ONLINE->value      => 'success',
                        GuideExperienceStatusEnum::VERFICATION->value => 'warning',
                        GuideExperienceStatusEnum::REFUSED->value,
                        GuideExperienceStatusEnum::TO_BE_COMPLETED->value => 'danger',
                        GuideExperienceStatusEnum::DELETED->value     => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('ville')
                    ->label('Ville')
                    ->icon('heroicon-m-map-pin')
                    ->placeholder('—'),

                TextColumn::make('prix_par_voyageur')
                    ->label('Prix / pers.')
                    ->money('EUR', divideBy: 1)
                    ->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->emptyStateHeading('Aucune expérience');
    }
}
