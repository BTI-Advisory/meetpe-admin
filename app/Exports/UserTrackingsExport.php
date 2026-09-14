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
        return ['Nom', 'Email', 'Date', 'Action', 'Type', 'Route', 'Adresse IP', 'Méthode', 'Métadonnées'];
    }

    public function map($record): array
    {
        return [
            $record->user?->name ?? '—',
            $record->user?->email ?? '—',
            $record->created_at?->format('d/m/Y H:i') ?? '',
            $record->action_label,
            $record->actor_type ?? '—',
            $record->route ?? '—',
            $record->ip_address ?? '—',
            $record->method ?? '—',
            $this->flattenMetadata($record->metadata),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,  // Nom
            'B' => 30,  // Email
            'C' => 16,  // Date
            'D' => 20,  // Action
            'E' => 12,  // Type
            'F' => 40,  // Route
            'G' => 16,  // IP
            'H' => 10,  // Méthode
            'I' => 45,  // Métadonnées
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row: bold + background
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'color' => ['rgb' => '374151']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Metadata column: wrap text + top-align
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('I2:I' . $lastRow)->getAlignment()
            ->setWrapText(true)
            ->setVertical(Alignment::VERTICAL_TOP);

        // All data rows: vertical top
        $sheet->getStyle('A2:H' . $lastRow)->getAlignment()
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
