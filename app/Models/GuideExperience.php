<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ReservationStatus;
use DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Carbon;

class GuideExperience extends Model
{
    use HasFactory;
    protected $fillable = [
        'categorie',
        'title','title_en',
        'description','description_en',
        "audio_file",
        'languages',      
        'duree',
        'prix_par_voyageur',
        'inclus',
        'nombre_des_voyageur',
        'type_des_voyageur',
        'ville','ville_en',
        'addresse',
        'code_postale',
        'lang',
        'lat',
        "user_id",
        "status",
        "country",'country_en',
        "timezone",
        "guide_personnes_peuves_participer",
        "et_avec_ça",
        "is_online",
        "support_group_prive",
        "price_group_prive",
        "discount_kids_between_2_and_12",
        "dernier_minute_reservation",
        "max_group_size"

    ];
    public function getTitleForLocale(string $locale): string
    {
        return match($locale) {
            'en' => $this->title_en,
            default => $this->title,
        };
    }

    public function getTitleAttribute($value)
    {
        $locale = explode(',', request()->header('Accept-Language', 'fr'))[0];
        return $locale === 'en' ? $this->title_en : $value;
    }

     public function getDescriptionAttribute($value)
    {
        $locale = explode(',', request()->header('Accept-Language', 'fr'))[0];
        return $locale === 'en' ? $this->description_en : $value;
    }

    public function getVilleAttribute($value)
    {
        $locale = explode(',', request()->header('Accept-Language', 'fr'))[0];
        return $locale === 'en' ? $this->ville_en : $value;
    }

    public function getCountryAttribute($value)
    {
        $locale = explode(',', request()->header('Accept-Language', 'fr'))[0];
        return $locale === 'en' ? $this->country_en : $value;
    }

    public function getStatusAttribute($value)
    {
        $locale = explode(',', request()->header('Accept-Language', 'fr'))[0];
        $locale = in_array($locale, ['fr','en']) ? $locale : 'fr';
        App::setLocale($locale);
        return __('choices.'.strval($value));
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class,'experience_id','id');
    }
    
    public function plannings()
    {
        return $this->hasMany(ExperiencePlannings::class,'experience_id','id');
    }

    public function dispoPlannings()
    {
        return $this->hasMany(ExperiencePlannings::class, 'experience_id', 'id')
                    ->where('start_date', '>=', Carbon::today());
    }
   
    public function planningWithoutAbscences()
    {
        return $this->plannings()->whereDoesntHave('guideAbsences', function ($query) {
            $query->where(function ($query) {
                $query->whereColumn('experience_plannings.start_date', '<=', 'guide_absence_times.date_to')
                      ->whereColumn('experience_plannings.end_date', '>=', 'guide_absence_times.date_from');
            });
        });
    }
    public function planningBookable($nbVoyageur)
    {
            return $this->planningWithoutAbscences()
            ->where('experience_plannings.start_date', '>=', now()->toDateString())
           // ->whereBetween('experience_plannings.start_date', ['2025-03-05', '2025-03-10']) 
            ->whereHas('schedules', function ($query) use ($nbVoyageur) {
                $query->leftJoin('reservations', function ($join) {
                    $join->on(
                        DB::raw('CONCAT(experience_plannings.start_date, " ", experience_schedules.start_time)'), '=', 'reservations.date_time')
                        ->whereNOTIn("reservations.status",[ReservationStatus::ACCEPTÉE->value,ReservationStatus::PENDING->value,ReservationStatus::CREATED->value]);                    
                })
                ->leftJoin('guide_experiences as e', 'e.id', '=', 'experience_plannings.experience_id') // Jointure avec guide_experiences pour accéder à nombre_des_voyageur
                ->selectRaw('experience_schedules.id, experience_schedules.start_time, experience_schedules.end_time, IFNULL(SUM(reservations.nombre_des_voyageurs), 0) AS total_reserved, e.nombre_des_voyageur')
                ->groupBy('experience_schedules.id', 'experience_schedules.start_time', 'experience_schedules.end_time', 'e.nombre_des_voyageur')
                ->havingRaw('e.nombre_des_voyageur - total_reserved >= ?', [$nbVoyageur]);
            });
    
    }
    public function planningBookableWithDateExact($nbVoyageur,$dateArriveeVoyageur, $dateDepartVoyageur)
    {
            return $this->planningWithoutAbscences()
            ->where('experience_plannings.start_date', '>=', now()->toDateString())
            ->whereBetween('experience_plannings.start_date', [$dateArriveeVoyageur, $dateDepartVoyageur]) 
            ->whereHas('schedules', function ($query) use ($nbVoyageur) {
                $query->leftJoin('reservations', function ($join) {
                    $join->on(
                        DB::raw('CONCAT(experience_plannings.start_date, " ", experience_schedules.start_time)'), '=', 'reservations.date_time')
                        ->whereNOTIn("reservations.status",[ReservationStatus::ACCEPTÉE->value,ReservationStatus::PENDING->value,ReservationStatus::CREATED->value]);                    
                })
                ->leftJoin('guide_experiences as e', 'e.id', '=', 'experience_plannings.experience_id') // Jointure avec guide_experiences pour accéder à nombre_des_voyageur
                ->selectRaw('experience_schedules.id, experience_schedules.start_time, experience_schedules.end_time, IFNULL(SUM(reservations.nombre_des_voyageurs), 0) AS total_reserved, e.nombre_des_voyageur')
                ->groupBy('experience_schedules.id', 'experience_schedules.start_time', 'experience_schedules.end_time', 'e.nombre_des_voyageur')
                ->havingRaw('e.nombre_des_voyageur - total_reserved >= ?', [$nbVoyageur]);
            });
    
    }
    
    
    public function likedExperiences()
    {
        return $this->hasMany(LikedExperience::class, "experience_id");
    }
    public function photos()
    {
        return $this->hasMany(GuidExperiencePhotos::class, "guide_experience_id")->where("type_image", '!=',"principal");
    }
    public function photoprincipal()
    {
        return $this->hasOne(GuidExperiencePhotos::class, "guide_experience_id")->where("type_image", "principal");
    }
    public function image_1()
    {
        return $this->hasOne(GuidExperiencePhotos::class, "guide_experience_id")->where("type_image", "image_0");
    }
    public function image_2()
    {
        return $this->hasOne(GuidExperiencePhotos::class, "guide_experience_id")->where("type_image", "image_1");
    }
    public function image_3()
    {
        return $this->hasOne(GuidExperiencePhotos::class, "guide_experience_id")->where("type_image", "image_2");
    }
    public function image_4()
    {
        return $this->hasOne(GuidExperiencePhotos::class, "guide_experience_id")->where("type_image", "image_3");
    }
    public function image_5()
    {
        return $this->hasOne(GuidExperiencePhotos::class, "guide_experience_id")->where("type_image", "image_4");
    }
    public function photononprincipal()
    {
        return $this->hasMany(GuidExperiencePhotos::class, "guide_experience_id")->where("type_image", '!=', "principal");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
    // Function definition
    public function scopeGetByDistance($query, $lat, $lang, $radius)
    {
        return $query->selectRaw('*, ( 3959 * acos( cos( radians(?) ) * cos( radians( lat ) ) * cos( radians( lang ) - radians(?) ) + sin( radians(?) ) * sin( radians(lat) ) ) ) AS distance', [$lat, $lang, $lat])
            ->having('distance', '<', $radius)
            ->orderBy('distance');
    }
    protected $casts = [
        "is_online" => "boolean",
        "support_group_prive" => "boolean",
        "discount_kids_between_2_and_12" => "boolean",
        "nombre_des_voyageur" => "integer",
    ];

    public function getFullAddress()
    {
        return $this->addresse.' '.$this->code_postale.' '.$this->ville.', '.$this->country;
    }

    public function avis()
    {
        return $this->hasMany(Avis::class, "experience_id");
    }
}
