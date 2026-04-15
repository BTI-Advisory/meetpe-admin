<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GuideAbsenceTime;
use App\Models\GuideExperience;
use Illuminate\Support\Facades\DB;
class ExperiencePlannings extends Model
{
    use HasFactory;
    protected $fillable = ["experience_id","start_date","end_date"];
    public function experience()
    {
        return $this->belongsTo(GuideExperience::class,"experience_id");
    }

    public function schedules()
    {
        return $this->hasMany(ExperienceSchedules::class,'planning_id','id');
    }

    public function schedulesBoockable($nbVoyageur = 0)
    {
        $startDate = $this->start_date; // Capture la date de début du planning
        
        $query = $this->hasMany(ExperienceSchedules::class, 'planning_id', 'id')
            ->leftJoin('reservations', function ($join) use ($startDate) {
                $join->on('reservations.date_time', '=', DB::raw("CONCAT('$startDate', ' ', experience_schedules.start_time)"))
                    ->whereRaw("DATE(reservations.date_time) = ?", [$startDate])
                    ->whereColumn('experience_schedules.start_time', '=', DB::raw("TIME(reservations.date_time)"))
                    ->where('reservations.is_group', true) // Créneaux réservés par un groupe
                    ->where('reservations.is_payed', true) // Seulement les réservations payées
                    ->where('reservations.status', 'Acceptée'); // Statut confirmé
            })
            ->whereNull('reservations.id') // Exclure les créneaux réservés par un groupe
            ->where(function ($query) use ($startDate, $nbVoyageur) {
                $query->whereRaw("
                    (SELECT IFNULL(SUM(r.nombre_des_voyageurs), 0)
                     FROM reservations r
                     WHERE DATE(r.date_time) = ?
                       AND TIME(r.date_time) = experience_schedules.start_time
                       AND r.is_group = false
                       AND r.is_payed = true
                       AND r.status = 'Acceptée') <=
                    (SELECT e.nombre_des_voyageur
                     FROM guide_experiences e
                     WHERE e.id = ?) - ?", 
                    [$startDate, $this->experience_id, max(1, $nbVoyageur)]); // Utilisation de max pour éviter une soustraction incorrecte
            })
            ->select('experience_schedules.*');
    
        return $query->get(); // Exécuter la requête et obtenir une collection
    }
    
    public function guideAbsences()
    {
        return $this->hasManyThrough(
            GuideAbsenceTime::class,
            GuideExperience::class,
            'id', // Clé étrangère dans GuideExperiences
            'user_id', // Clé étrangère dans GuideAbsenceTime
            'experience_id', // Clé locale dans ExperiencePlanning
            'user_id' // Clé locale dans GuideExperiences
        );
    }
}
