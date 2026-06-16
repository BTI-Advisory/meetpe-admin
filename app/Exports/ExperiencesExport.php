<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\GuideExperience;
use Illuminate\Support\Facades\DB;

class ExperiencesExport implements FromGenerator, WithHeadings
{
    protected $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function generator(): \Generator
    {
        $offset    = 0;
        $chunkSize = 100;

        while (true) {
            $experiences = GuideExperience::with(['plannings.schedules', 'user'])
                ->when(!empty($this->status), fn ($q) => $q->where('status', $this->status))
                ->orderBy('id')
                ->offset($offset)
                ->limit($chunkSize)
                ->get();

            if ($experiences->isEmpty()) break;

            $ids = $experiences->pluck('id')->toArray();

            $allResponses = DB::table('responses')
                ->join('question_choices', 'responses.choice_id', '=', 'question_choices.id')
                ->whereIn('responses.entity_id', $ids)
                ->where('responses.entity', 'experience')
                ->whereIn('question_choices.question_id', [5, 6])
                ->select('responses.entity_id', 'question_choices.question_id', 'question_choices.choice_txt')
                ->get()
                ->groupBy('entity_id');

            foreach ($experiences as $experience) {
                $plannings  = $experience->plannings;
                $expResp    = $allResponses->get($experience->id, collect())->groupBy('question_id');
                $categories = $expResp->get(5, collect())->pluck('choice_txt')->implode(', ');
                $languages  = $expResp->get(6, collect())->pluck('choice_txt')->implode(', ');
                $guideLine  = $experience->user->name . ' | ' . $experience->user->email . "\n" . $experience->user->phone_number;

                $isFirstExperienceLine = true;

                if ($plannings->isEmpty()) {
                    yield [
                        $experience->id,
                        $experience->title,
                        $guideLine,
                        $experience->status,
                        $experience->duree,
                        $experience->getFullAddress(),
                        $experience->nombre_des_voyageur,
                        $experience->prix_par_voyageur,
                        $experience->support_group_prive ? 'OUI' : 'NON',
                        $experience->price_group_prive ?? '',
                        $experience->min_group_size_prive ?? '',
                        $experience->max_group_size ?? '',
                        $experience->discount_kids_between_2_and_12 ? 'OUI' : 'NON',
                        $categories,
                        $languages,
                        null,
                        null,
                        $experience->created_at->format('Y-m-d'),
                    ];
                } else {
                    foreach ($plannings as $planning) {
                        $isFirstPlanningLine = true;

                        foreach ($planning->schedules as $schedule) {
                            yield [
                                $isFirstExperienceLine ? $experience->id : '',
                                $isFirstExperienceLine ? $experience->title : '',
                                $isFirstExperienceLine ? $guideLine : '',
                                $isFirstExperienceLine ? $experience->status : '',
                                $isFirstExperienceLine ? $experience->duree : '',
                                $isFirstExperienceLine ? $experience->getFullAddress() : '',
                                $isFirstExperienceLine ? $experience->nombre_des_voyageur : '',
                                $isFirstExperienceLine ? $experience->prix_par_voyageur . ' €' : '',
                                $isFirstExperienceLine ? ($experience->support_group_prive ? 'OUI' : 'NON') : '',
                                $isFirstExperienceLine ? ($experience->price_group_prive ?? '') : '',
                                $isFirstExperienceLine ? ($experience->min_group_size_prive ?? '') : '',
                                $isFirstExperienceLine ? ($experience->max_group_size ?? '') : '',
                                $isFirstExperienceLine ? ($experience->discount_kids_between_2_and_12 ? 'OUI' : 'NON') : '',
                                $isFirstExperienceLine ? $categories : '',
                                $isFirstExperienceLine ? $languages : '',
                                $isFirstPlanningLine ? ($planning->start_date . ' - ' . $planning->end_date) : '',
                                $schedule->start_time . ' - ' . $schedule->end_time,
                                $isFirstExperienceLine ? $experience->created_at->format('Y-m-d') : '',
                            ];

                            $isFirstExperienceLine = false;
                            $isFirstPlanningLine   = false;
                        }
                    }
                }
            }

            unset($experiences, $allResponses);
            $offset += $chunkSize;
        }
    }

    public function headings(): array
    {
        return [
            'ID',
            'Titre',
            'Guide',
            'Statut',
            'Durée',
            'Adresse complète',
            'Nombre de voyageurs Max',
            'Prix par voyageur',
            'Groupe privé autorisé',
            'Prix par groupe',
            'Nombre de voyageurs min par groupe',
            'Nombre de voyageurs max par groupe',
            'Réduction enfants -12 ans',
            'Catégories',
            'Langues',
            'Date planning',
            'Créneau horaire',
            'Date de création',
        ];
    }
}
