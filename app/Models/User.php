<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\UserRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        "otp_code",
        "user_type",
        "profile_path",
        "siren_number",
        "name_of_company",
        "is_tva_applicable",
        "phone_number",
        "is_full_available",
        "IBAN",
        "BIC",
        "nom_du_titulaire",
        "rue",
        "ville",
        "code_postal",
        "fcm_token",
        "has_updated_hes_schedule",
        "piece_d_identite",
        "KBIS_file",
        "is_verified_account",
        "piece_d_identite_verso",
        "about_me",
        'about_me_en',
        "about_me_audio",
        'device_language'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_full_available' => 'boolean',
        'has_updated_hes_schedule' => 'boolean',
        'is_verified' => 'boolean',
        'is_verified_account' => 'boolean',
    ];
    public function getAboutMeAttribute($value)
    {
        // Récupère la langue demandée dans le header (fr par défaut)
        $locale = explode(',', request()->header('Accept-Language', 'fr'))[0];

        // Si EN → retourne about_me_en
        return strtolower($locale) === 'en'
            ? $this->about_me_en
            : $value; // about_me en FR
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, "user_roles")->select("role_name");
    }
    public function userRoles()
    {
        return $this->hasMany(UserRoles::class, "user_id");
    }
    public function Guide()
    {
        return $this->hasMany(Guide::class, "user_id");
    }
    public function Voyageur()
    {
        return $this->hasMany(Voyageur::class, "user_id");
    }
    public function routeNotificationForVonage(Notification $notification): string
    {
        return $this->phone_number;
    }
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }
    public function otherDocuments()
    {
        return $this->hasMany(OtherDocument::class, "user_id");
    }
    
    public function absences()
    {
        return $this->hasMany(GuideAbsenceTime::class, 'user_id');
    }
    
    public function experiences()
    {
        return $this->hasMany(GuideExperience::class, 'user_id');
    }
    public function likedExperiences()
    {
        return $this->hasMany(LikedExperience::class, 'user_id');
    }
    public function notificationSettings()
    {
        return $this->hasOne(NotificationSettings::class);
    }
    public function responses()
    {
        return $this->hasMany(Responses::class, 'user_id');
    }
    public function reservationsVoyageur()
    {
        return $this->hasMany(Reservation::class, 'voyageur_id');
    }
    public function devices()
    {
        return $this->hasOne(UserDEvice::class, 'user_id');
    }
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'user_id');
    }

    public function getAgeAttribute(): int
    {
        if (!$this->birth_date) {
            return 0;
        }

        return \Carbon\Carbon::parse($this->birth_date)->age;
    }

    public function chatChannelUsers()
    {
        return $this->hasMany(ChatChannelUser::class, 'user_id');
    }

    public function chatChannels()
    {
        return $this->hasManyThrough(
            ChatChannel::class,
            ChatChannelUser::class,
            'user_id',     // Foreign key on ChatChannelUser table
            'id',          // Foreign key on ChatChannel table
            'id',          // Local key on User table
            'channel_id'   // Local key on ChatChannelUser table
        );
    }

    public function isProWithTva(): bool
    {
        return !empty($this->siren_number) && $this->is_tva_applicable;
    }
    public function isLocal(): bool
    {
        return empty($this->siren_number) && !$this->is_tva_applicable;
    }

    public function autofacturationConsents()
    {
        return $this->hasMany(UserAutofacturationConsent::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return UserRoles::where('user_id', $this->id)->where('role_id', 3)->exists();
    }
}
