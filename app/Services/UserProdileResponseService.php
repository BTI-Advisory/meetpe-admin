<?php

namespace App\Services;

use App\Models\User;

class UserProdileResponseService
{
    private array $personnalite;
    private array $type_de_voyageur;
    private array $style_de_voyage_prefere;
    private array $environnement_prefere;
    private array $type_d_experience;
    private array $type_rencentre;
    private array $languages;
    private User $user;

    /**
     * @param array $personnalite
     * @param array $type_de_voyageur
     * @param array $style_de_voyage_prefere
     * @param array $environnement_prefere
     * @param array $type_d_experience
     * @param array $type_rencentre
     * @param array $languages
     * @param User $user
     */
    public function __construct(array $personnalite, array $type_de_voyageur, array $style_de_voyage_prefere, array $environnement_prefere, array $type_d_experience, array $type_rencentre, array $languages, User $user)
    {
        $this->personnalite = $personnalite;
        $this->type_de_voyageur = $type_de_voyageur;
        $this->style_de_voyage_prefere = $style_de_voyage_prefere;
        $this->environnement_prefere = $environnement_prefere;
        $this->type_d_experience = $type_d_experience;
        $this->type_rencentre = $type_rencentre;
        $this->languages = $languages;
        $this->user = $user;
    }


    public function SaveProfileReponse(){
        foreach ($this->personnalite as $v_p){
            echo $v_p;
        }
    }
//    public function GenerateResponseProfileVoyageur():array{
//        $response_to_save = [];
//        foreach ($this->languages as $l){
//            $langs =
//        }
//        return $response_to_save;
//    }
}
