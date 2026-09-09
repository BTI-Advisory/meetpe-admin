<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserTrackingsExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, WithStyles
{
    public function __construct(private Builder $query) {}

    public function query(): Builder
    {
        return $this->query;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function map($record): array
    {
        return [
            $record->created_at?->format('d/m/Y H:i') ?? '',
            $record->action_label,
            $record->actor_type ?? '—',
            $record->route ?? '—',
            $record->ip_address ?? '—',
            $record->method ?? '—',
        ];
    }

    public function headings(): array
    {
        return ['Date', 'Action', 'Type', 'Route', 'Adresse IP', 'Méthode'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
