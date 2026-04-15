<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\GuideExperience;
use App\Models\Responses;
class ExperiencesExport implements FromArray, WithHeadings
{
    protected $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function array(): array
{
    $rows = [];
    $experiences = GuideExperience::with([
        'plannings.schedules'
    ])->where('status', $this->status)->get();

    foreach ($experiences as $experience) {
        $plannings = $experience?->dispoPlannings ?? [];

        $isFirstExperienceLine = true;

        if ($plannings->isEmpty()) {
            $rows[] = [
                $experience->id,
                $experience->title,
                // ajouter le guide de l'expérience
                $experience->user->name . " | ".$experience->user->email ."\n".$experience->user->phone_number,
                $experience->status,
                $experience->duree,
                $experience->getFullAddress(),

                $experience->nombre_des_voyageur,
                $experience->prix_par_voyageur,

                $experience->support_group_prive? "OUI":"NON",
                $experience->price_group_prive?? '',
                $experience->max_group_size??'',

                $experience->discount_kids_between_2_and_12?'OUI':"NON",

                Responses::getChoicesOf(explode(',', $experience->categorie))
                ->map(function ($choice) {
                    return $choice->choix;
                })
                ->implode('\n'),

                Responses::getChoicesOf(explode(',', $experience->languages))
                ->map(function ($choice) {
                    return $choice->choix;
                })
                ->implode('\n'),

                Responses::getChoicesOf(explode(',', $experience->guide_personnes_peuves_participer))
                ->map(function ($choice) {
                    return $choice->choix;
                })
                ->implode('\n'),
                
                Responses::getChoicesOf(explode(',', $experience->et_avec_ça))
                ->map(function ($choice) {
                    return $choice->choix;
                })
                ->implode('\n'),
                

                null,
                null,
                $experience->created_at->format('Y-m-d'),
            ];
        } else {
            foreach ($plannings as $planning) {
                $schedules = $planning->schedules;
                $isFirstPlanningLine = true;

                foreach ($schedules as $schedule) {
                    $rows[] = [
                        $isFirstExperienceLine ? $experience->id : '',
                        $isFirstExperienceLine ? $experience->title : '',
                        $isFirstExperienceLine ?$experience->user->name . " | ".$experience->user->email ."\n".$experience->user->phone_number :"",

                        $isFirstExperienceLine ? $experience->status : '',
                        $isFirstExperienceLine ? $experience->duree : '',
                        $isFirstExperienceLine ? $experience->getFullAddress() : '',

                        $isFirstExperienceLine ? $experience->nombre_des_voyageur: '',
                        $isFirstExperienceLine ? $experience->prix_par_voyageur .' €': '',

                        $isFirstExperienceLine ? ($experience->support_group_prive? "OUI":"NON"):'',
                        $isFirstExperienceLine ? ($experience->price_group_prive?? ''):'',
                        $isFirstExperienceLine ? ($experience->max_group_size?? ''):'',

                        $isFirstExperienceLine ? $experience->discount_kids_between_2_and_12?'OUI':"NON" :'',

                        $isFirstExperienceLine
                        ? Responses::getChoicesOf(explode(',', $experience->categorie))
                            ->map(function ($choice) {
                                return $choice->choix;
                            })
                            ->implode('\n')
                        : '',

                        $isFirstExperienceLine
                        ? Responses::getChoicesOf(explode(',', $experience->languages))
                            ->map(function ($choice) {
                                return $choice->choix;
                            })
                            ->implode('\n')
                        : '',


                        $isFirstExperienceLine
                        ? Responses::getChoicesOf(explode(',', $experience->guide_personnes_peuves_participer))
                            ->map(function ($choice) {
                                return $choice->choix;
                            })
                            ->implode('\n')
                        : '',

                        $isFirstExperienceLine
                        ? Responses::getChoicesOf(explode(',', $experience->et_avec_ça))
                            ->map(function ($choice) {
                                return $choice->choix;
                            })
                            ->implode('\n')
                        : '',
                        
                        $isFirstPlanningLine ? ($planning->start_date . ' - ' . $planning->end_date) : '',
                        $schedule->start_time . ' - ' . $schedule->end_time,
                        $isFirstExperienceLine ? $experience->created_at->format('Y-m-d') : '',
                    ];

                    $isFirstExperienceLine = false;
                    $isFirstPlanningLine = false;
                }
            }
        }
    }

    return $rows;
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

            'Groupe privé authorisé',
            'Prix par groupe',
            'Nombre de voyageur max par groupe',

            'Réduction enfants -12 ans',

            'Catégories',
            'Langues',
            'personnes qui peuvent participer',
            'Et avec ça',

            
            'Date planning',
            'Créneau horaire',
            'Date de création',
        ];
    }
}
