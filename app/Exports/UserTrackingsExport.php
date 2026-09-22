<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserTrackingsExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, WithStyles, WithColumnWidths
{
    public function __construct(private Builder $query) {}

    public function query(): Builder
    {
        return $this->query
            ->select(
                'user_trackings.id',
                'user_trackings.user_id',
                'user_trackings.created_at',
                'user_trackings.action',
                'user_trackings.actor_type',
                'user_trackings.route',
                'user_trackings.ip_address',
                'user_trackings.method',
                'user_trackings.metadata',
            )
            ->with('user:id,name,email');
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function headings(): array
    {
        return ['ID utilisateur', 'Nom', 'Email', 'Date', 'Type d\'action', 'Action', 'Route', 'Adresse IP', 'Méthode', 'Métadonnées'];
    }

    public function map($record): array
    {
        return [
            $record->user_id,
            $record->user?->name ?? '—',
            $record->user?->email ?? '—',
            $record->created_at?->format('d/m/Y H:i') ?? '',
            $record->actor_type ?? '—',
            $record->action_label,
            $record->route ?? '—',
            $record->ip_address ?? '—',
            $record->method ?? '—',
            $this->flattenMetadata($record->metadata),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14,  // ID utilisateur
            'B' => 22,  // Nom
            'C' => 30,  // Email
            'D' => 16,  // Date
            'E' => 14,  // Type d'action
            'F' => 20,  // Action
            'G' => 40,  // Route
            'H' => 16,  // IP
            'I' => 10,  // Méthode
            'J' => 45,  // Métadonnées
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row: bold + background
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'color' => ['rgb' => '374151']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Metadata column: wrap text + top-align
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('J2:J' . $lastRow)->getAlignment()
            ->setWrapText(true)
            ->setVertical(Alignment::VERTICAL_TOP);

        // All data rows: vertical top
        $sheet->getStyle('A2:I' . $lastRow)->getAlignment()
            ->setVertical(Alignment::VERTICAL_TOP);

        return [];
    }

    private function flattenMetadata(?array $data, string $prefix = '', int $maxChars = 800): string
    {
        if (empty($data)) return '';

        $lines = [];
        foreach ($data as $key => $value) {
            $label = $prefix ? $prefix . '.' . $key : $key;
            if (is_array($value)) {
                $nested = $this->flattenMetadata($value, $label, $maxChars);
                if ($nested !== '') {
                    $lines[] = $nested;
                }
            } else {
                $lines[] = $label . ': ' . $value;
            }
        }

        $result = implode("\n", $lines);

        if (mb_strlen($result) > $maxChars) {
            $result = mb_substr($result, 0, $maxChars) . "\n[... tronqué]";
        }

        return $result;
    }
}
