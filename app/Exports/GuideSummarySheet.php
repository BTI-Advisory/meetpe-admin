<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class GuideSummarySheet implements FromArray, WithTitle
{
    protected $guide;

    public function __construct($guide)
    {
        $this->guide = $guide;
    }

    public function array(): array
    {
        $allRatings = $this->guide->experiences()->with('avis')->get()
            ->pluck('avis')
            ->flatten()
            ->pluck('note');

        $average = $allRatings->count() ? round($allRatings->avg(), 2) : 'Aucune note';

        return [
            ['Nom du guide', $this->guide->name],
            ['Note moyenne', $average . ' / 5'],
            ['Nombre total d’avis', $allRatings->count()],
        ];
    }

    public function title(): string
    {
        return 'Résumé Guide';
    }
}
