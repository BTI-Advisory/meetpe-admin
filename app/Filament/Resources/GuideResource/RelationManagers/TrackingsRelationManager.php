<?php

namespace App\Filament\Resources\GuideResource\RelationManagers;

use App\Enums\TrackingAction;
use App\Exports\UserTrackingsExport;
use App\Models\UserTracking;
use Carbon\Carbon;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\DatePicker;

class TrackingsRelationManager extends RelationManager
{
    protected static string $relationship = 'userTrackings';
    protected static ?string $title = 'Historique (3 mois)';
    protected static ?string $icon = 'heroicon-o-clock';

    public function table(Table $table): Table
    {
        $component = $this;

        return $table
            ->query(fn () => UserTracking::where('user_id', $this->getOwnerRecord()->id)
                ->where('actor_type', 'guide')
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
            ->filters([
                Filter::make('date')
                    ->label('Période')
                    ->form([
                        DatePicker::make('date_from')->label('Du'),
                        DatePicker::make('date_to')->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $query
                            ->when($data['date_from'], fn ($q) => $q->whereDate('created_at', '>=', $data['date_from']))
                            ->when($data['date_to'], fn ($q) => $q->whereDate('created_at', '<=', $data['date_to']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators[] = 'Du : ' . $data['date_from'];
                        }
                        if ($data['date_to'] ?? null) {
                            $indicators[] = 'Au : ' . $data['date_to'];
                        }
                        return $indicators;
                    }),

                SelectFilter::make('action')
                    ->label('Action')
                    ->options(
                        collect(TrackingAction::cases())
                            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
                            ->toArray()
                    ),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Exporter Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () use ($component) {
                        $filters = $component->tableFilters ?? [];

                        // Pour l'export, pas de limite 3 mois par défaut — on applique les filtres admin
                        $query = UserTracking::where('user_id', $component->getOwnerRecord()->id)
                            ->where('actor_type', 'guide');

                        if (!empty($filters['date']['date_from'])) {
                            $query->whereDate('created_at', '>=', $filters['date']['date_from']);
                        }
                        if (!empty($filters['date']['date_to'])) {
                            $query->whereDate('created_at', '<=', $filters['date']['date_to']);
                        }
                        if (!empty($filters['action']['value'])) {
                            $query->where('action', $filters['action']['value']);
                        }

                        return Excel::download(
                            new UserTrackingsExport($query->latest()),
                            'historique-actions-guide.xlsx'
                        );
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100])
            ->emptyStateHeading('Aucune action enregistrée');
    }
}
