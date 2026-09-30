<?php

namespace App\Http\Controllers\Crm;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\GuideExperience;
use App\Models\Reservation;
use App\Models\User;
use App\Models\UserTracking;
use App\Models\Voyageur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    private function perPage(Request $request): int
    {
        return min((int) $request->get('per_page', 50), 100);
    }

    private function applyDateFilters($query, Request $request, string $column = 'created_at'): void
    {
        if ($request->filled('from')) {
            $query->whereDate($column, '>=', $request->get('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate($column, '<=', $request->get('to'));
        }
        if ($request->filled('updated_since')) {
            $query->where('updated_at', '>=', $request->get('updated_since'));
        }
    }

    // ─────────────────────────────────────────────
    // GET /api/v1/crm/guides
    // ─────────────────────────────────────────────
    public function guides(Request $request): JsonResponse
    {
        $request->validate([
            'from'          => 'nullable|date',
            'to'            => 'nullable|date',
            'updated_since' => 'nullable|date',
            'per_page'      => 'nullable|integer|min:1|max:100',
        ]);

        $query = User::whereHas('guide')
            ->with(['guide', 'experiences'])
            ->orderByDesc('created_at');

        $this->applyDateFilters($query, $request);

        $paginated = $query->paginate($this->perPage($request));

        $data = $paginated->getCollection()->map(function (User $u) {
            $g            = $u->guide->first();
            $experiences  = $u->experiences ?? collect();
            $reservations = $experiences->flatMap(fn ($e) => $e->reservations ?? collect());

            return [
                'guide_id'              => $g?->guide_id,
                'user_id'               => $u->id,
                'nom'                   => $u->name,
                'email'                 => $u->email,
                'telephone'             => $u->phone_number,
                'date_naissance'        => $u->birth_date,
                'siren'                 => $u->siren_number,
                'societe'               => $u->name_of_company,
                'tva_applicable'        => (bool) $u->is_tva_applicable,
                'stripe_statut'         => $g?->stripe_connect_form_status,
                'stripe_account_id'     => $g?->stripe_account_id,
                'compte_verifie'        => (bool) $u->is_verified_account,
                'statut'                => $u->is_verified_account ? 'Actif' : 'Inactif',
                'date_inscription'      => $u->created_at?->toDateString(),
                'bio_fr'                => $u->about_me,
                'bio_audio_url'         => $u->about_me_audio,
                'photo_url'             => $u->profile_path,
                'nb_experiences'        => $experiences->count(),
                'nb_experiences_online' => $experiences->where('status', 'online')->count(),
                'nb_reservations'       => $reservations->count(),
                'updated_at'            => $u->updated_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'data'  => $data,
            'meta'  => [
                'current_page' => $paginated->currentPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'last_page'    => $paginated->lastPage(),
            ],
            'links' => [
                'first' => $paginated->url(1),
                'last'  => $paginated->url($paginated->lastPage()),
                'prev'  => $paginated->previousPageUrl(),
                'next'  => $paginated->nextPageUrl(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /api/v1/crm/voyageurs
    // ─────────────────────────────────────────────
    public function voyageurs(Request $request): JsonResponse
    {
        $request->validate([
            'from'          => 'nullable|date',
            'to'            => 'nullable|date',
            'updated_since' => 'nullable|date',
            'per_page'      => 'nullable|integer|min:1|max:100',
        ]);

        $query = Voyageur::with(['user', 'reservations'])
            ->orderByDesc('created_at');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->get('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->get('to'));
        }
        if ($request->filled('updated_since')) {
            $query->where('updated_at', '>=', $request->get('updated_since'));
        }

        $paginated = $query->paginate($this->perPage($request));

        $data = $paginated->getCollection()->map(function (Voyageur $v) {
            $u = $v->user;
            return [
                'voyageur_id'     => $v->voyageur_id,
                'user_id'         => $v->user_id,
                'nom'             => $u?->name,
                'email'           => $u?->email,
                'telephone'       => $u?->phone_number,
                'date_naissance'  => $u?->birth_date,
                'ville'           => $v->ville,
                'pays'            => $v->pays,
                'statut'          => $u?->is_verified_account ? 'Actif' : 'Inactif',
                'date_inscription'=> $u?->created_at?->toDateString(),
                'langue_app'      => $u?->device_language,
                'nb_reservations' => $v->reservations->count(),
                'updated_at'      => $v->updated_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'data'  => $data,
            'meta'  => [
                'current_page' => $paginated->currentPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'last_page'    => $paginated->lastPage(),
            ],
            'links' => [
                'first' => $paginated->url(1),
                'last'  => $paginated->url($paginated->lastPage()),
                'prev'  => $paginated->previousPageUrl(),
                'next'  => $paginated->nextPageUrl(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /api/v1/crm/experiences
    // ─────────────────────────────────────────────
    public function experiences(Request $request): JsonResponse
    {
        $request->validate([
            'from'          => 'nullable|date',
            'to'            => 'nullable|date',
            'updated_since' => 'nullable|date',
            'per_page'      => 'nullable|integer|min:1|max:100',
            'status'        => 'nullable|string',
            'guide_id'      => 'nullable|integer',
        ]);

        $query = GuideExperience::with([
                'dispoPlannings.schedules',
                'user.guide',
                'conditions',
                'infos',
                'photoprincipal',
                'photos',
            ])
            ->orderByDesc('created_at');

        $this->applyDateFilters($query, $request);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('guide_id')) {
            $query->whereHas('user.guide', fn ($q) => $q->where('guide_id', $request->get('guide_id')));
        }

        $paginated = $query->paginate($this->perPage($request));

        // Preload reserved seat counts for all schedules in this page in one query
        $scheduleIds = $paginated->getCollection()->flatMap(
            fn ($e) => $e->dispoPlannings->flatMap(fn ($p) => $p->schedules->pluck('id'))
        )->unique()->all();

        $reservedBySchedule = \Illuminate\Support\Facades\DB::table('reservations')
            ->join('experience_schedules', function ($j) {
                $j->on(\Illuminate\Support\Facades\DB::raw("TIME(reservations.date_time)"), '=', 'experience_schedules.start_time');
            })
            ->whereIn('experience_schedules.id', $scheduleIds)
            ->whereIn('reservations.status', ['Acceptée', 'En attente'])
            ->where('reservations.is_payed', true)
            ->groupBy('experience_schedules.id')
            ->select('experience_schedules.id as schedule_id', \Illuminate\Support\Facades\DB::raw('SUM(reservations.nombre_des_voyageurs) as total_reserved'))
            ->pluck('total_reserved', 'schedule_id');

        $data = $paginated->getCollection()->map(function (GuideExperience $e) use ($reservedBySchedule) {
            $guideId   = $e->user->guide->first()?->guide_id ?? null;
            $capacite  = (int) $e->nombre_des_voyageur;
            $condition = $e->conditions;
            $infos     = $e->infos->groupBy('type');

            $creneaux = $e->dispoPlannings->flatMap(function ($planning) use ($e, $capacite, $reservedBySchedule) {
                return $planning->schedules->map(function ($s) use ($e, $planning, $capacite, $reservedBySchedule) {
                    $reserved  = (int) ($reservedBySchedule[$s->id] ?? 0);
                    $restantes = max(0, $capacite - $reserved);
                    $passe     = now()->gt($planning->start_date . ' ' . $s->start_time);
                    $statut    = $passe ? 'passé' : ($restantes === 0 ? 'complet' : 'disponible');

                    return [
                        'slot_id'          => $s->id,
                        'experience_id'    => $e->id,
                        'date'             => $planning->start_date,
                        'heure_debut'      => $s->start_time,
                        'heure_fin'        => $s->end_time,
                        'capacite'         => $capacite,
                        'nb_reserved'      => $reserved,
                        'places_restantes' => $restantes,
                        'statut'           => $statut,
                        'updated_at'       => $s->updated_at?->toIso8601String(),
                    ];
                });
            })->values()->all();

            return [
                'experience_id'     => $e->id,
                'guide_id'          => $guideId,
                'titre'             => $e->title,
                'statut'            => $e->status,
                'date_creation'     => $e->created_at?->toDateString(),
                'description'       => $e->description,
                'inclus'            => $e->inclus,
                'ville'             => $e->ville,
                'pays'              => $e->country,
                'adresse'           => $e->addresse,
                'code_postal'       => $e->code_postale,
                'duree'             => $e->duree,
                'prix_par_voyageur' => $e->prix_par_voyageur,
                'max_voyageurs'     => $capacite,
                'groupe_prive'      => (bool) $e->support_group_prive,
                'prix_groupe_prive' => $e->price_group_prive,
                'min_groupe'        => $e->min_group_size_prive,
                'reduction_enfants' => (bool) $e->discount_kids_between_2_and_12,
                'difficulte'        => $condition?->difficulty,
                'pmr_accessible'    => (bool) $condition?->pmr_accessible,
                'equipement_fourni' => $condition?->equipment_included,
                'tenue_requise'     => $condition?->outfit_required,
                'repas_inclus'      => (bool) $condition?->meal_included,
                'a_apporter'        => $infos->get('to_bring', collect())->pluck('content')->filter()->values()->all(),
                'bon_a_savoir'      => $infos->get('good_to_know', collect())->pluck('content')->filter()->values()->all(),
                'photo_couverture'  => $e->photoprincipal?->photo_url,
                'photos'            => $e->photos->pluck('photo_url')->filter()->values()->all(),
                'nom_guide'         => $e->user?->name,
                'email_guide'       => $e->user?->email,
                'creneaux_futurs'   => $creneaux,
                'updated_at'        => $e->updated_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'data'  => $data,
            'meta'  => [
                'current_page' => $paginated->currentPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'last_page'    => $paginated->lastPage(),
            ],
            'links' => [
                'first' => $paginated->url(1),
                'last'  => $paginated->url($paginated->lastPage()),
                'prev'  => $paginated->previousPageUrl(),
                'next'  => $paginated->nextPageUrl(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /api/v1/crm/reservations
    // ─────────────────────────────────────────────
    public function reservations(Request $request): JsonResponse
    {
        $request->validate([
            'from'          => 'nullable|date',
            'to'            => 'nullable|date',
            'updated_since' => 'nullable|date',
            'per_page'      => 'nullable|integer|min:1|max:100',
            'status'        => 'nullable|string',
        ]);

        $query = Reservation::with(['experience.user', 'voyageur'])
            ->whereNotIn('status', [ReservationStatus::CREATED->value, ReservationStatus::ABANDONED->value])
            ->orderByDesc('created_at');

        $this->applyDateFilters($query, $request);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $paginated = $query->paginate($this->perPage($request));

        $data = $paginated->getCollection()->map(function (Reservation $r) {
            return [
                'reservation_id'    => $r->id,
                'voyageur_id'       => $r->voyageur_id,
                'experience_id'     => $r->experience_id,
                'date_creation'     => $r->created_at?->toIso8601String(),
                'date_experience'   => $r->date_time,
                'ville'             => $r->experience?->ville,
                'titre_experience'  => $r->experience?->title,
                'nom_guide'         => $r->experience?->user?->name,
                'email_guide'       => $r->experience?->user?->email,
                'nom_voyageur'      => $r->voyageur?->name ?? $r->nom,
                'email_voyageur'    => $r->voyageur?->email,
                'statut'              => $r->status,
                'statut_paiement'     => $r->is_payed ? 'Payé' : 'Non payé',
                'nb_participants'     => $r->nombre_des_voyageurs,
                'type'                => $r->is_group ? 'Groupe privé' : 'Individuel',
                'montant'             => $r->total_price,
                'montant_guide'       => $r->guide_payout_amount,
                'montant_rembourse'   => $r->refund_amount,
                'statut_remboursement'=> $r->stripe_refund_status,
                'motif_annulation'    => $r->cancel_reason,
                'detail_annulation'   => $r->cancel_description,
                'annule_le'           => $r->canceled_at,
                'updated_at'          => $r->updated_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'data'  => $data,
            'meta'  => [
                'current_page' => $paginated->currentPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'last_page'    => $paginated->lastPage(),
            ],
            'links' => [
                'first' => $paginated->url(1),
                'last'  => $paginated->url($paginated->lastPage()),
                'prev'  => $paginated->previousPageUrl(),
                'next'  => $paginated->nextPageUrl(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /api/v1/crm/reservations/incomplete
    // ─────────────────────────────────────────────
    public function reservationsIncomplete(Request $request): JsonResponse
    {
        $request->validate([
            'from'          => 'nullable|date',
            'to'            => 'nullable|date',
            'updated_since' => 'nullable|date',
            'per_page'      => 'nullable|integer|min:1|max:100',
        ]);

        $query = Reservation::with(['experience', 'voyageur'])
            ->whereIn('status', [ReservationStatus::CREATED->value, ReservationStatus::ABANDONED->value])
            ->orderByDesc('created_at');

        $this->applyDateFilters($query, $request);

        $paginated = $query->paginate($this->perPage($request));

        $data = $paginated->getCollection()->map(function (Reservation $r) {
            return [
                'reservation_id'    => $r->id,
                'voyageur_id'       => $r->voyageur_id,
                'experience_id'     => $r->experience_id,
                'date_tentative'    => $r->created_at?->toIso8601String(),
                'date_experience'   => $r->date_time,
                'nb_participants'   => $r->nombre_des_voyageurs,
                'montant_potentiel' => $r->total_price,
                'statut'            => $r->status,
                'etape_estimee'     => $this->estimateStep($r),
                'nom_voyageur'      => $r->voyageur?->name ?? $r->nom,
                'email_voyageur'    => $r->voyageur?->email,
                'titre_experience'  => $r->experience?->title,
                'updated_at'        => $r->updated_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'data'  => $data,
            'meta'  => [
                'current_page' => $paginated->currentPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'last_page'    => $paginated->lastPage(),
            ],
            'links' => [
                'first' => $paginated->url(1),
                'last'  => $paginated->url($paginated->lastPage()),
                'prev'  => $paginated->previousPageUrl(),
                'next'  => $paginated->nextPageUrl(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /api/v1/crm/trackings
    // ─────────────────────────────────────────────
    public function trackings(Request $request): JsonResponse
    {
        $request->validate([
            'from'          => 'nullable|date',
            'to'            => 'nullable|date',
            'updated_since' => 'nullable|date',
            'per_page'      => 'nullable|integer|min:1|max:100',
            'actor_type'    => 'nullable|string',
            'user_id'       => 'nullable|integer',
        ]);

        $query = UserTracking::with('user:id,name,email')
            ->orderByDesc('created_at');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->get('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->get('to'));
        }
        if ($request->filled('updated_since')) {
            $query->where('created_at', '>=', $request->get('updated_since'));
        }
        if ($request->filled('actor_type')) {
            $query->where('actor_type', $request->get('actor_type'));
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        $paginated = $query->paginate($this->perPage($request));

        $data = $paginated->getCollection()->map(function (UserTracking $t) {
            return [
                'id'          => $t->id,
                'user_id'     => $t->user_id,
                'nom'         => $t->user?->name,
                'email'       => $t->user?->email,
                'date'        => $t->created_at?->toIso8601String(),
                'type_action' => $t->actor_type,
                'action'      => $t->action_label ?? $t->action,
                'route'       => $t->route,
                'ip'          => $t->ip_address,
            ];
        });

        return response()->json([
            'data'  => $data,
            'meta'  => [
                'current_page' => $paginated->currentPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'last_page'    => $paginated->lastPage(),
            ],
            'links' => [
                'first' => $paginated->url(1),
                'last'  => $paginated->url($paginated->lastPage()),
                'prev'  => $paginated->previousPageUrl(),
                'next'  => $paginated->nextPageUrl(),
            ],
        ]);
    }

    private function estimateStep(Reservation $r): string
    {
        if ($r->stripe_payment_error) {
            return 'Echec paiement : ' . $r->stripe_payment_error;
        }
        if ($r->stripe_payment_intent_id) {
            return 'Paiement initié, non finalisé';
        }
        return 'Abandon avant paiement';
    }
}
