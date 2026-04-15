<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\GuideExperience;
use App\Models\GuidExperiencePhotos;
use App\Models\OtherDocument;
use App\Models\QuestionChoice;
use App\Models\Responses;
use App\Models\User;
use App\Models\UserDEvice;
use App\Models\Voyageur;
use App\Notifications\DocumentsSupplementaires;
use App\Notifications\MakeExperienceNonComplete;
use App\Notifications\YourExperienceIsValid;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ExperienceResource;
use App\Http\Resources\VoyageurResource;
use Illuminate\Support\Facades\URL;
use App\Enums\GuideExperienceStatusEnum;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Notifications\MailStripeConnectURLForGuide;
use App\Services\StripeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class AdminGuideController extends Controller
{
 
    public function getExperienceByStatus($status)
    {

        $perPage = 20; // Nombre d'éléments par page
    
        // Charger uniquement les relations nécessaires pour éviter une charge mémoire excessive
        $query = GuideExperience::with([
            'photoprincipal',
            'photononprincipal',
            'user',
            'plannings.schedules'
        ])
            ->where('status', $status)
            ->orderByDesc('created_at');
    
        // Paginer les résultats directement via la requête
        $paginatedExperiences = $query->paginate($perPage);
    
        // Transformer les données avec la resource
        $resourceCollection = ExperienceResource::collection(
            $paginatedExperiences->getCollection()->map(function ($experience){
                return new ExperienceResource($experience, true);
            })
        );
    
        // Créer un nouveau LengthAwarePaginator avec les resources transformées
        $transformedPaginator = new LengthAwarePaginator(
            $resourceCollection->toArray(request()), // Récupère les données transformées
            $paginatedExperiences->total(),
            $paginatedExperiences->perPage(),
            $paginatedExperiences->currentPage(),
            ['path' => $paginatedExperiences->path()]
        );
    
        //dd($transformedPaginator);
        // Retourner la vue avec les expériences paginées
        return view('dashboard.table', [
            'experiences' =>  $transformedPaginator,
            'status'=> $status
        ]);
    }
    public function getGuideExperiences(int $index = 0)
    {
        return $this->getExperienceByStatus(GuideExperienceStatusEnum::VERFICATION->value);
    }
    
    
    public function getNonCompleted(int $index = 0)
    {
        return $this->getExperienceByStatus(GuideExperienceStatusEnum::TO_BE_COMPLETED->value);
    }
    public function getOnlineExperiences(int $index = 0)
    {
        return $this->getExperienceByStatus(GuideExperienceStatusEnum::ONLINE->value);
    }
    public function getRejectedGuidExperiences(int $index = 0)
    {
        return $this->getExperienceByStatus(GuideExperienceStatusEnum::REFUSED->value);
    }
    public function getArchivedGuidExperiences(int $index = 0)
    {
        return $this->getExperienceByStatus(GuideExperienceStatusEnum::ARCHIVED->value);
    }
    public function getHorsLigneGuidExperiences(int $index = 0)
    {
        return $this->getExperienceByStatus(GuideExperienceStatusEnum::OFFLINE->value);
    }
    public function getDeletedExperiences(int $index = 0)
    {
        return $this->getExperienceByStatus(GuideExperienceStatusEnum::DELETED->value);
    }
    public function getGuide(int $index = 0)
    {
        $perPage = 20;
        $guides = User::whereHas('guide', function ($query) {
                $query->whereNotNull('user_id');
            })->with("guide")
            ->orderByDesc("created_at")
            ->get();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $guides->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $absolutePath = env("APP_URL_FORCE") . "/guides"; // Get the full URL without query parameters

        $paginatedguides = new LengthAwarePaginator(
            $currentPageItems,
            count($guides),
            $perPage,
            $currentPage,
            ['path' => $absolutePath]
        );

        return view('dashboard.guide')->with("guides", $paginatedguides);
    }
    public function guidesFiles(int $user_id)
    {
        $user = User::find($user_id);
        $device = UserDEvice::where("user_id", $user->id)->first();
        $other_document = OtherDocument::where("user_id", $user->id)->get();

    
        // Charger uniquement les relations nécessaires pour éviter une charge mémoire excessive
        $query = GuideExperience::with([
            'photoprincipal',
            'photononprincipal',
            'user',
            'plannings.schedules'
        ])
            ->where('user_id', $user_id)
            ->orderByDesc('updated_at');
    
        // Paginer les résultats directement via la requête
        $paginatedExperiences = $query->paginate(10);
    
        // Transformer les données avec la resource
        $resourceCollection = ExperienceResource::collection($paginatedExperiences->getCollection());
    
        // Créer un nouveau LengthAwarePaginator avec les resources transformées
        $transformedPaginator = new LengthAwarePaginator(
            $resourceCollection->toArray(request()), // Récupère les données transformées
            $paginatedExperiences->total(),
            $paginatedExperiences->perPage(),
            $paginatedExperiences->currentPage(),
            ['path' => $paginatedExperiences->path()]
        );


        return view('dashboard.files')->with("guide", $user)
            ->with("device", $device)
            ->with("other_document", $other_document)
            ->with("experiences",$transformedPaginator);
    }
    public function createGuide(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255",
            //"profile_path"=>"string",
            "siren_number" => "nullable|string|max:255",
            "phone_number" => "required",

        ]);
        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "siren_number" => $request->siren_number,
            "password" => Hash::make("password"),
            "phone_number" => $request->phone_number ?? "no phone",
            "user_type" => "guide",
            "profile_path" => "dsdqs",
            'fcm_token' => 'fcm_token',
        ]);
        return redirect()->route("getGuide");
    }
    public function UpdateStatus(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            "new_status" => "required",
        ]);

        // Find the guide experience by its ID
        $experience = GuideExperience::find($id);
        $user = User::find($experience->user_id);
        App::setLocale($user->device_language);
        if ($request->new_status == "accepter") {
            // Update the status of the guide experience
            $experience->status = GuideExperienceStatusEnum::ONLINE->value;
            $user->notify(new YourExperienceIsValid($user->fcm_token, $experience->getTitleForLocale($user->device_language)));
            //return $channels;

            // send notif

            // Save the changes to the database
            $experience->save();
        }
        if ($request->new_status == "refuser") {
            // Update the status of the guide experience
            $experience->status = GuideExperienceStatusEnum::REFUSED->value;
            // send notif

            // Save the changes to the database
            $experience->save();
        }
        if ($request->new_status == "autre_document") {
            // Update the status of the guide experience
            $experience->status = "autre_document";


            // Save the changes to the database
            $experience->save();
            $user->notify(new DocumentsSupplementaires($user->fcm_token, $experience->getTitleForLocale($user->device_language)));
        }

        // Return a JSON response indicating success
        return redirect()->back();
    }
    public function another_document(int $index = 0)
    {
        return $this->getExperienceByStatus(GuideExperienceStatusEnum::DOCUMENT->value);
    }
    public function addComment(Request $request, $id)
    {
        $request->validate([
            "raison" => "required",
        ]);
        $experience = GuideExperience::find($id);
        $experience->raison = $request->raison;
        $experience->status = GuideExperienceStatusEnum::TO_BE_COMPLETED->value;
        $experience->save();
        //  return $experience;
        $user = User::find($experience->user_id);
        App::setLocale($user->device_language);
        $user->notify(new MakeExperienceNonComplete($request->raison, $experience->getTitleForLocale($user->device_language), $user->fcm_token));
        return redirect()->back();
    }
   
    public function add_GuideExperience($id)
    {
        $data = QuestionChoice::all();
        $voyage_mode_fr = [];
        $voyage_type_fr = [];
        $voyage_preference_fr = [];
        $personalite_fr = [];
        $voyageur_experiences = [];
        $voyageur_rencontre_fr = [];
        $guide_truc_de_toi_fr = [];
        $languages_fr = [];
        $guide_categorie_de_lexperience = [];
        $et_avec_ça = [];
        $guide_personnes_peuves_participer = [];
        $reservation_de_dernier_minute = [];
        foreach ($data as $d) {
            if ($d["choice_key"] == "voyage_mode_fr") {
                array_push($voyage_mode_fr, $d);
            }
            if ($d["choice_key"] == "voyage_type_fr") {
                array_push($voyage_type_fr, $d);
            }
            if ($d["choice_key"] == "voyage_preference_fr") {
                array_push($voyage_preference_fr, $d);
            }
            if ($d["choice_key"] == "personalite_fr") {
                array_push($personalite_fr, $d);
            }
            if ($d["choice_key"] == "voyageur_experiences") {
                array_push($voyageur_experiences, $d);
            }
            if ($d["choice_key"] == "voyageur_rencontre_fr") {
                array_push($voyageur_rencontre_fr, $d);
            }
            if ($d["choice_key"] == "guide_truc_de_toi_fr") {
                array_push($guide_truc_de_toi_fr, $d);
            }
            if ($d["choice_key"] == "languages_fr") {
                array_push($languages_fr, $d);
            }
            if ($d["choice_key"] == "guide_categorie_de_lexperience") {
                array_push($guide_categorie_de_lexperience, $d);
            }
            if ($d["choice_key"] == "et_avec_ça") {
                array_push($et_avec_ça, $d);
            }
            if ($d["choice_key"] == "guide_personnes_peuves_participer") {
                array_push($guide_personnes_peuves_participer, $d);
            }
            if ($d["choice_key"] == "reservation_de_dernier_minute") {
                array_push($reservation_de_dernier_minute, $d);
            }
        }
        //return $voyage_type_fr;
        //$categories = QuestionChoice::where("choice_key", "guide_categorie_de_lexperience")->select(["choice_txt", "id"])->get();
        //return $categories;
        // $guide_personnes_peuves_participer = QuestionChoice::where("choice_key", "guide_personnes_peuves_participer")->select(["choice_txt", "id"])->get();
        // $avec_ca = QuestionChoice::where("choice_key", "et_avec_ça")->select(["choice_txt", "id"])->get();
        return view('dashboard.add-guideexperience')
            ->with("voyage_mode_fr", $voyage_mode_fr)
            ->with("voyage_type_fr", $voyage_type_fr)
            ->with("voyage_preference_fr", $voyage_preference_fr)
            ->with("personalite_fr", $personalite_fr)
            ->with("voyageur_experiences", $voyageur_experiences)
            ->with("voyageur_rencontre_fr", $voyageur_rencontre_fr)
            ->with("guide_truc_de_toi_fr", $guide_truc_de_toi_fr)
            ->with("languages_fr", $languages_fr)
            ->with("guide_categorie_de_lexperience", $guide_categorie_de_lexperience)
            ->with("et_avec_ça", $et_avec_ça)
            ->with("guide_personnes_peuves_participer", $guide_personnes_peuves_participer)
            ->with("reservation_de_dernier_minute", $reservation_de_dernier_minute)
            ->with("id", $id);
    }


    public function getAllReservations(int $index=0)
    {
        $perPage = 20;
        $reservations = Reservation::whereNotIn("status",[ReservationStatus::CREATED->value])
        ->with(['experience','experience.user','voyageur'])
        ->orderBy("date_time")
        ->get();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $reservations->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $absolutePath = env("APP_URL_FORCE") . "/reservations"; // Get the full URL without query parameters

        $paginatedResa = new LengthAwarePaginator(
            $currentPageItems,
            count($reservations),
            $perPage,
            $currentPage,
            ['path' => $absolutePath]
        );

        //dd($paginatedResa);
        return view('dashboard.reservations')->with("reservations", $paginatedResa);
    }

    public function getAllVoyageurs($index = 0)
    {
        $perPage = 20;

        // Obtenir les voyageurs avec leur relation `user`
        $voyageurs = Voyageur::whereHas('user', function ($query) {
            $query->whereNotNull('id');
        })->with('user');
    

        $paginatedVoyageurs = $voyageurs->paginate($perPage);
        $VoyageursCollection = VoyageurResource::collection($paginatedVoyageurs->getCollection());

        // Créer un nouveau LengthAwarePaginator avec les resources transformées
        $transformedPaginator = new LengthAwarePaginator(
            $VoyageursCollection->toArray(request()), // Récupère les données transformées
            $paginatedVoyageurs->total(),
            $paginatedVoyageurs->perPage(),
            $paginatedVoyageurs->currentPage(),
            ['path' => $paginatedVoyageurs->path()]
        );

        // Passer les données transformées à la vue
        return view('dashboard.voyageurs')->with("voyageurs", $transformedPaginator);
    }

    public function getDashboard()
    {
        $stats = [
            'guides'                    => User::whereHas('Guide')->count(),
            'voyageurs'                 => Voyageur::count(),
            'experiences_en_ligne'      => GuideExperience::where('status', GuideExperienceStatusEnum::ONLINE->value)->count(),
            'experiences_verification'  => GuideExperience::where('status', GuideExperienceStatusEnum::VERFICATION->value)->count(),
            'reservations_total'        => Reservation::whereNotIn('status', [ReservationStatus::CREATED->value, ReservationStatus::ABANDONED->value])->count(),
            'reservations_acceptees'    => Reservation::where('status', ReservationStatus::ACCEPTÉE->value)->count(),
        ];

        $recent_reservations = Reservation::whereNotIn('status', [ReservationStatus::CREATED->value])
            ->with(['experience', 'experience.user'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.dashboard', compact('stats', 'recent_reservations'));
    }

    public function getVoyageursDetails($UserId)
    {
        $user = User::findOrFail($UserId);
        $voyageur = Voyageur::where('user_id', $UserId)->first();

        if (!$voyageur) {
            abort(404);
        }

        $voyageurData = (new VoyageurResource($voyageur))->toArray(request());
        $reservations = Reservation::where('voyageur_id', $UserId)
            ->whereNotIn('status', [ReservationStatus::CREATED->value])
            ->with(['experience', 'experience.user', 'experience.photoprincipal'])
            ->orderByDesc('date_time')
            ->get();

        return view('dashboard.voyageur-detail', [
            'voyageur'     => $voyageurData,
            'user'         => $user,
            'reservations' => $reservations,
        ]);
    }

    public function createStripeAccountForGuide($userId, StripeService $stripeService)
    {
        $user = User::find($userId);
        $guide = Guide::where('user_id',$userId)->first();
        App::setlocale($user->device_language);
        //créer les compte connect stripe pour le guide
        $isAccountCreated = $stripeService->createStripeAccountForGuide($user);
        if(!is_bool($isAccountCreated))
        {
            $guide->stripe_connect_form_status = "sent";
            $guide->save();
            $user->notify(new MailStripeConnectURLForGuide($user->fcm_token,$guide->stripe_connect_form_url));
            Log::channel('notification_nails')->info('DASHBOARD_ACTION : notification MailStripeConnectURLForGuide has been sent to this mail ' . $user->email);
        }
        return redirect('/guides');
    }
   
}
