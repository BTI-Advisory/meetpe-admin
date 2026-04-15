<?php

namespace App\Filament\Resources\GuideExperienceResource\Pages;

use App\Enums\GuideExperienceStatusEnum;
use App\Exports\ExperiencesExport;
use App\Filament\Resources\GuideExperienceResource;
use Filament\Actions\Action;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ListGuideExperiences extends ListRecords
{
    protected static string $resource = GuideExperienceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Exporter')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $status = $this->activeTab ?? GuideExperienceStatusEnum::VERFICATION->value;
                    return Excel::download(new ExperiencesExport($status), 'experiences-' . $status . '.xlsx');
                }),
        ];
    }

    public function getTabs(): array
    {
        $count = fn (GuideExperienceStatusEnum $status) => \App\Models\GuideExperience::where('status', $status->value)->count();

        return [
            'en_ligne' => Tab::make('En ligne')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', GuideExperienceStatusEnum::ONLINE->value))
                ->badge($count(GuideExperienceStatusEnum::ONLINE)),

            'verification' => Tab::make('En vérification')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', GuideExperienceStatusEnum::VERFICATION->value))
                ->badge($count(GuideExperienceStatusEnum::VERFICATION)),

            'a_completer' => Tab::make('À compléter')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', GuideExperienceStatusEnum::TO_BE_COMPLETED->value))
                ->badge($count(GuideExperienceStatusEnum::TO_BE_COMPLETED)),

            'autre_document' => Tab::make('Doc. supplémentaire')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', GuideExperienceStatusEnum::DOCUMENT->value))
                ->badge($count(GuideExperienceStatusEnum::DOCUMENT)),

            'hors_ligne' => Tab::make('Hors ligne')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', GuideExperienceStatusEnum::OFFLINE->value))
                ->badge($count(GuideExperienceStatusEnum::OFFLINE)),

            'refusee' => Tab::make('Refusées')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', GuideExperienceStatusEnum::REFUSED->value))
                ->badge($count(GuideExperienceStatusEnum::REFUSED)),

            'archivee' => Tab::make('Archivées')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', GuideExperienceStatusEnum::ARCHIVED->value))
                ->badge($count(GuideExperienceStatusEnum::ARCHIVED)),

            'supprimee' => Tab::make('Supprimées')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', GuideExperienceStatusEnum::DELETED->value))
                ->badge($count(GuideExperienceStatusEnum::DELETED)),
        ];
    }
}
