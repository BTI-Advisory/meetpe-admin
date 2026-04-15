<?php
namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\User;

class AvisExport implements WithMultipleSheets
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function sheets(): array
    {
        $sheets = [];

        $user =  User::find($this->userId);
        // Feuille par expérience
        foreach ($user->experiences as $experience) {
            $sheets[] = new ExperienceReviewsSheet($experience);
        }

        // Feuille moyenne globale
        $sheets[] = new GuideSummarySheet($user);

        return $sheets;
    }
}
