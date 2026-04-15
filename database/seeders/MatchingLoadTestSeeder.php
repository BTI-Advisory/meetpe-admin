<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MatchingLoadTestSeeder extends Seeder
{
    private const SUFFIX = '@loadtest.local';
    // bcrypt('password') pré-hashé pour éviter 2450 hash au runtime
    private const PW = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

    private array $cities = [
        ['name' => 'Paris',       'lat' => 48.8566,  'lng' =>  2.3522],
        ['name' => 'Lyon',        'lat' => 45.7640,  'lng' =>  4.8357],
        ['name' => 'Marseille',   'lat' => 43.2965,  'lng' =>  5.3698],
        ['name' => 'Bordeaux',    'lat' => 44.8378,  'lng' => -0.5792],
        ['name' => 'Toulouse',    'lat' => 43.6047,  'lng' =>  1.4442],
        ['name' => 'Nice',        'lat' => 43.7102,  'lng' =>  7.2620],
        ['name' => 'Nantes',      'lat' => 47.2184,  'lng' => -1.5536],
        ['name' => 'Strasbourg',  'lat' => 48.5734,  'lng' =>  7.7521],
        ['name' => 'Montpellier', 'lat' => 43.6108,  'lng' =>  3.8767],
        ['name' => 'Lille',       'lat' => 50.6292,  'lng' =>  3.0573],
        ['name' => 'Rennes',      'lat' => 48.1173,  'lng' => -1.6778],
        ['name' => 'Grenoble',    'lat' => 45.1885,  'lng' =>  5.7245],
        ['name' => 'Toulon',      'lat' => 43.1242,  'lng' =>  5.9280],
        ['name' => 'Reims',       'lat' => 49.2583,  'lng' =>  4.0317],
        ['name' => 'Angers',      'lat' => 47.4784,  'lng' => -0.5632],
    ];

    private array $photos = [
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1776075684_image_picker_4190211C-DD5C-4092-BCB4-E34B6E144A62-3660-0000003558DC1C10.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1759416526_image_picker_F1340FFD-4D08-4FE1-9263-3D4BD258826A-13307-000003C637057205.png',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1769526729_image_picker_1B5078EA-74ED-4F66-A6EA-A418A8440FEF-29137-000004BFD0E940C0.png',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1742225591_image_picker_B6D14A68-D7FC-4E30-9F34-E6730A542004-58980-000000971199EDB9.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1743494751_image_picker_B51D820B-D47B-4992-A6B7-67E30C6AEE8B-11786-000000197E73DE09.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1743495021_image_picker_878C488E-D52C-4548-933F-ED729E0FDCD4-11786-0000001A94E48A1D.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1743500846_image_picker_A903B676-2548-44BD-B18C-D7FFA2B390C5-33021-0000003746CC0C59.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1744123817_image_picker_075EB1A2-28CE-4746-8F9C-F15EAF49E355-62991-0000038E08A84684.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1744124418_image_picker_51FE43D5-7ED9-4A1A-A6B0-22157C661FFA-62991-00000390DAD83A57.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1744124987_image_picker_D569FC7C-44B7-44B8-9498-623BB70455CE-62991-00000394DC7CBCEA.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1744125396_image_picker_DA005C68-49B6-4E9C-847C-6FA2AC0EFDF1-62991-00000397281174A4.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1744125550_image_picker_74D9B547-5EBA-43A0-880B-4743C2F6A9F8-62991-00000398032532D9.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1744126376_image_picker_72B685ED-CCDF-40A4-85DB-BF644B573B1F-62991-0000039C8AA27FB1.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1744126837_image_picker_C68569D2-3509-4A5E-8FA1-60FF9ACF057C-62991-0000039F36811ABE.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1744127067_image_picker_2CC098CA-674F-4173-9000-9CC838A50482-62991-000003A0829CBD7E.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1744193876_image_picker_A21CE835-6E7C-4366-B145-5DEB7296EEC8-62991-000003E41CCC11E8.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1744194031_image_picker_A56FAEF6-D975-4816-8FC2-777454A9D160-62991-000003E4E44C3829.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1744272669_image_picker_D0DD7749-3DF2-4A70-B550-4D4EBC5F1620-62991-0000042C965F4E35.jpg',
        'https://meetpe-dev-bucket.s3.amazonaws.com/experiences/1745921428_image_picker_EE3C901C-46ED-4E33-A1F1-573CA27EEA8A-67498-0000014FC8235BA8.jpg',
    ];

    private array $timeSlots = [
        ['start' => '09:00:00', 'end' => '11:00:00'],
        ['start' => '11:00:00', 'end' => '13:00:00'],
        ['start' => '14:00:00', 'end' => '16:00:00'],
        ['start' => '16:00:00', 'end' => '18:00:00'],
        ['start' => '18:00:00', 'end' => '20:00:00'],
    ];

    // ────────────────────────────────────────────────────────────────────────────

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // ── 1. Nettoyage ──────────────────────────────────────────────────────
        $this->command->info('[1/6] Nettoyage des données de test précédentes...');
        $this->clean();

        // ── 2. Chargement des questions / choix ───────────────────────────────
        $this->command->info('[2/6] Chargement des questions/choix depuis la BDD...');
        [$langQId, $langIds, $catQId, $catIds, $delayQId, $delayIds] = $this->loadChoices();

        $now   = now()->toDateTimeString();
        $today = Carbon::today();

        // ── 3. Guides ─────────────────────────────────────────────────────────
        $this->command->info('[3/6] Création de 450 guides...');

        $guideUserRows = [];
        for ($i = 1; $i <= 450; $i++) {
            $city = $this->randomCity();
            $guideUserRows[] = [
                'name'                     => "Guide Test $i",
                'email'                    => "guide_$i" . self::SUFFIX,
                'password'                 => self::PW,
                'user_type'                => 'guide',
                'profile_path'             => '',
                'phone_number'             => '',
                'otp_code'                 => '',
                'fcm_token'                => '',
                'has_updated_hes_schedule' => 0,
                'is_verified_account'      => 1,
                'ville'                    => $city['name'],
                'created_at'               => $now,
                'updated_at'               => $now,
            ];
        }
        foreach (array_chunk($guideUserRows, 200) as $chunk) {
            DB::table('users')->insert($chunk);
        }

        $guideUserIds = DB::table('users')
            ->where('email', 'like', 'guide_%' . self::SUFFIX)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        $guideProfiles = array_map(fn ($uid) => [
            'user_id'    => $uid,
            'pro_local'  => 'local',
            'created_at' => $now,
            'updated_at' => $now,
        ], $guideUserIds);
        foreach (array_chunk($guideProfiles, 200) as $chunk) {
            DB::table('guides')->insert($chunk);
        }

        // ── 4. Expériences + plannings + schedules + réponses expérience ──────
        $this->command->info('[4/6] Création de 2000 expériences + créneaux + réponses...');

        $guideCount   = count($guideUserIds);
        $expPerGuide  = intdiv(2000, $guideCount);
        $extraGuides  = 2000 % $guideCount;

        $expRows  = [];
        $expIndex = 0;
        foreach ($guideUserIds as $gIdx => $uid) {
            $count = $expPerGuide + ($gIdx < $extraGuides ? 1 : 0);
            for ($j = 0; $j < $count; $j++) {
                $city      = $this->randomCity();
                $expRows[] = [
                    'user_id'                        => $uid,
                    'title'                          => "Expérience Test $expIndex",
                    'title_en'                       => "Test Experience $expIndex",
                    'description'                    => 'Description de test pour le seeder de charge.',
                    'description_en'                 => 'Load test seed experience description.',
                    'status'                         => 'en ligne',
                    'ville'                          => $city['name'],
                    'ville_en'                       => $city['name'],
                    'country'                        => 'France',
                    'country_en'                     => 'France',
                    'lat'                            => round($city['lat'] + (mt_rand(-800, 800) / 10000), 6),
                    'lang'                           => round($city['lng'] + (mt_rand(-800, 800) / 10000), 6),
                    'prix_par_voyageur'              => mt_rand(20, 200),
                    'nombre_des_voyageur'            => mt_rand(4, 15),
                    'duree'                          => mt_rand(1, 5) . 'h',
                    'timezone'                       => 'Europe/Paris',
                    'is_online'                      => 0,
                    'support_group_prive'            => 0,
                    'discount_kids_between_2_and_12' => 0,
                    'addresse'                       => '1 rue de Test',
                    'code_postale'                   => '75001',
                    'created_at'                     => $now,
                    'updated_at'                     => $now,
                ];
                $expIndex++;
            }
        }
        foreach (array_chunk($expRows, 200) as $chunk) {
            DB::table('guide_experiences')->insert($chunk);
        }

        // Récupérer les expériences avec leur guide user_id
        $experiences = DB::table('guide_experiences')
            ->whereIn('user_id', $guideUserIds)
            ->select('id', 'user_id')
            ->orderBy('id')
            ->get();

        $expIds = $experiences->pluck('id')->toArray();

        // Plannings
        $planningRows = [];
        foreach ($expIds as $eid) {
            $nPlannings = mt_rand(2, 4);
            for ($p = 0; $p < $nPlannings; $p++) {
                $offset    = mt_rand(1, 180);
                $startDate = $today->copy()->addDays($offset)->toDateString();
                $endDate   = $today->copy()->addDays($offset + mt_rand(0, 6))->toDateString();
                $planningRows[] = [
                    'experience_id' => $eid,
                    'start_date'    => $startDate,
                    'end_date'      => $endDate,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }
        }
        foreach (array_chunk($planningRows, 500) as $chunk) {
            DB::table('experience_plannings')->insert($chunk);
        }

        $planningIds = DB::table('experience_plannings')
            ->whereIn('experience_id', $expIds)
            ->pluck('id')
            ->toArray();

        // Schedules
        $scheduleRows = [];
        foreach ($planningIds as $pid) {
            $slots = $this->randomItems($this->timeSlots, mt_rand(1, 3));
            foreach ($slots as $slot) {
                $scheduleRows[] = [
                    'planning_id' => $pid,
                    'start_time'  => $slot['start'],
                    'end_time'    => $slot['end'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }
        foreach (array_chunk($scheduleRows, 1000) as $chunk) {
            DB::table('experience_schedules')->insert($chunk);
        }

        // Photos principales (rotation sur les 19 URLs disponibles)
        $photoRows  = [];
        $photoCount = count($this->photos);
        foreach ($expIds as $i => $eid) {
            $photoRows[] = [
                'guide_experience_id' => $eid,
                'photo_url'           => $this->photos[$i % $photoCount],
                'type_image'          => 'principal',
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
        }
        foreach (array_chunk($photoRows, 500) as $chunk) {
            DB::table('guid_experience_photos')->insert($chunk);
        }

        // Réponses expériences (langues + catégories + délai dernière minute)
        $expResponseRows = [];
        foreach ($experiences as $exp) {
            $eid         = $exp->id;
            $guideUserId = $exp->user_id;

            foreach ($this->randomItems($langIds, mt_rand(1, 3)) as $choiceId) {
                $expResponseRows[] = $this->responseRow($guideUserId, $choiceId, $langQId, 'experience', $eid, $now);
            }

            foreach ($this->randomItems($catIds, mt_rand(1, 3)) as $choiceId) {
                $expResponseRows[] = $this->responseRow($guideUserId, $choiceId, $catQId, 'experience', $eid, $now);
            }

            // Délai dernière minute : 1 seul choix par expérience
            $expResponseRows[] = $this->responseRow(
                $guideUserId,
                $this->randomItems($delayIds, 1)[0],
                $delayQId,
                'experience',
                $eid,
                $now
            );
        }
        foreach (array_chunk($expResponseRows, 1000) as $chunk) {
            DB::table('responses')->insert($chunk);
        }

        // ── 5. Voyageurs ──────────────────────────────────────────────────────
        $this->command->info('[5/6] Création de 2000 voyageurs...');

        $voyUserRows = [];
        for ($i = 1; $i <= 2000; $i++) {
            $city = $this->randomCity();
            $voyUserRows[] = [
                'name'                     => "Voyageur Test $i",
                'email'                    => "voyageur_$i" . self::SUFFIX,
                'password'                 => self::PW,
                'user_type'                => 'voyageur',
                'profile_path'             => '',
                'phone_number'             => '',
                'otp_code'                 => '',
                'fcm_token'                => '',
                'has_updated_hes_schedule' => 0,
                'is_verified_account'      => 1,
                'ville'                    => $city['name'],
                'created_at'               => $now,
                'updated_at'               => $now,
            ];
        }
        foreach (array_chunk($voyUserRows, 200) as $chunk) {
            DB::table('users')->insert($chunk);
        }

        $voyUserIds = DB::table('users')
            ->where('email', 'like', 'voyageur_%' . self::SUFFIX)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        $voyProfileRows  = [];
        $voyResponseRows = [];

        foreach ($voyUserIds as $uid) {
            $city       = $this->randomCity();
            $daysStart  = mt_rand(5, 90);
            $dateArrive = $today->copy()->addDays($daysStart)->toDateString();
            $dateDepart = $today->copy()->addDays($daysStart + mt_rand(3, 14))->toDateString();

            $voyProfileRows[] = [
                'user_id'      => $uid,
                'ville'        => $city['name'],
                'pays'         => 'France',
                'lat'          => round($city['lat'] + (mt_rand(-500, 500) / 10000), 6),
                'lang'         => round($city['lng'] + (mt_rand(-500, 500) / 10000), 6),
                'date_arrivee' => $dateArrive,
                'date_depart'  => $dateDepart,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];

            foreach ($this->randomItems($langIds, mt_rand(1, 2)) as $choiceId) {
                $voyResponseRows[] = $this->responseRow($uid, $choiceId, $langQId, 'voyageur', $uid, $now);
            }

            foreach ($this->randomItems($catIds, mt_rand(1, 3)) as $choiceId) {
                $voyResponseRows[] = $this->responseRow($uid, $choiceId, $catQId, 'voyageur', $uid, $now);
            }
        }

        foreach (array_chunk($voyProfileRows, 500) as $chunk) {
            DB::table('voyageurs')->insert($chunk);
        }
        foreach (array_chunk($voyResponseRows, 1000) as $chunk) {
            DB::table('responses')->insert($chunk);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ── 6. Résumé ──────────────────────────────────────────────────────────
        $this->command->info('[6/6] ✅ Données de test créées avec succès !');
        $this->command->table(
            ['Type', 'Quantité'],
            [
                ['Guides',       count($guideUserIds)],
                ['Expériences',  count($expIds)],
                ['Photos',       count($photoRows)],
                ['Plannings',    count($planningIds)],
                ['Schedules',    count($scheduleRows)],
                ['Voyageurs',    count($voyUserIds)],
                ['Réponses exp', count($expResponseRows)],
                ['Réponses voy', count($voyResponseRows)],
            ]
        );
    }

    // ── Nettoyage des données précédentes ────────────────────────────────────

    private function clean(): void
    {
        $guideIds = DB::table('users')
            ->where('email', 'like', 'guide_%' . self::SUFFIX)
            ->pluck('id');

        $voyIds = DB::table('users')
            ->where('email', 'like', 'voyageur_%' . self::SUFFIX)
            ->pluck('id');

        if ($guideIds->isNotEmpty()) {
            $expIds = DB::table('guide_experiences')
                ->whereIn('user_id', $guideIds)
                ->pluck('id');

            if ($expIds->isNotEmpty()) {
                $planIds = DB::table('experience_plannings')
                    ->whereIn('experience_id', $expIds)
                    ->pluck('id');

                if ($planIds->isNotEmpty()) {
                    DB::table('experience_schedules')->whereIn('planning_id', $planIds)->delete();
                }
                DB::table('experience_plannings')->whereIn('experience_id', $expIds)->delete();
                DB::table('responses')
                    ->where('entity', 'experience')
                    ->whereIn('entity_id', $expIds)
                    ->delete();
                DB::table('guid_experience_photos')->whereIn('guide_experience_id', $expIds)->delete();
                DB::table('guide_experiences')->whereIn('id', $expIds)->delete();
            }

            DB::table('guides')->whereIn('user_id', $guideIds)->delete();
            DB::table('users')->whereIn('id', $guideIds)->delete();
        }

        if ($voyIds->isNotEmpty()) {
            DB::table('responses')
                ->where('entity', 'voyageur')
                ->whereIn('entity_id', $voyIds)
                ->delete();
            DB::table('voyageurs')->whereIn('user_id', $voyIds)->delete();
            DB::table('users')->whereIn('id', $voyIds)->delete();
        }
    }

    // ── Chargement des questions et choix ────────────────────────────────────

    private function loadChoices(): array
    {
        $langQ  = DB::table('questions')->where('question_key', 'languages_fr')->first();
        $catQ   = DB::table('questions')->where('question_key', 'voyageur_experiences')->first();
        $delayQ = DB::table('questions')->where('question_key', 'reservation_de_dernier_minute')->first();

        if (! $langQ || ! $catQ || ! $delayQ) {
            $this->command->error('Questions manquantes en BDD. Lance d\'abord : php artisan db:seed --class=UpdateVoyageurQuestionsSeeder');
            exit(1);
        }

        $langIds  = DB::table('question_choices')->where('question_id', $langQ->id)->pluck('id')->toArray();
        $catIds   = DB::table('question_choices')
            ->where('question_id', $catQ->id)
            ->where('choice_key', '!=', 'NO_IDEA')
            ->pluck('id')
            ->toArray();
        $delayIds = DB::table('question_choices')->where('question_id', $delayQ->id)->pluck('id')->toArray();

        return [$langQ->id, $langIds, $catQ->id, $catIds, $delayQ->id, $delayIds];
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function randomCity(): array
    {
        return $this->cities[array_rand($this->cities)];
    }

    /**
     * Retourne $n éléments aléatoires d'un tableau (sans répétition).
     */
    private function randomItems(array $arr, int $n): array
    {
        $copy = $arr;
        shuffle($copy);
        return array_slice($copy, 0, min($n, count($copy)));
    }

    private function responseRow(
        int $userId,
        int $choiceId,
        int $questionId,
        string $entity,
        int $entityId,
        string $now
    ): array {
        return [
            'user_id'     => $userId,
            'choice_id'   => $choiceId,
            'question_id' => $questionId,
            'entity'      => $entity,
            'entity_id'   => $entityId,
            'created_at'  => $now,
            'updated_at'  => $now,
        ];
    }
}
