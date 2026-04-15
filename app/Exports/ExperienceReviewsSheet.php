<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Str;


class ExperienceReviewsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $experience;

    public function __construct($experience)
    {
        $this->experience = $experience;
    }

    public function collection()
    {
        return $this->experience->avis()->with('user')->get()->map(function ($review) {
            return [
                'Voyageur' => $review->user->name,
                'Note' => $review->note,
                'Commentaire' => $review->message,
                'Date'        => $review->created_at->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Nom Voyageur', 'Note', 'Commentaire', 'Date'];
    }
    public function title(): string
    {
        return Str::limit($this->experience->title, 31); // 31 max pour Excel
    }
}
