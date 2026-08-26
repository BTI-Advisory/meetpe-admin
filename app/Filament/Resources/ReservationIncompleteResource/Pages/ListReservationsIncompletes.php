<?php

namespace App\Filament\Resources\ReservationIncompleteResource\Pages;

use App\Filament\Resources\ReservationIncompleteResource;
use Filament\Resources\Pages\ListRecords;

class ListReservationsIncompletes extends ListRecords
{
    protected static string $resource = ReservationIncompleteResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
