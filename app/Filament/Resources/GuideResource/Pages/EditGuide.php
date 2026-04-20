<?php

namespace App\Filament\Resources\GuideResource\Pages;

use App\Filament\Resources\GuideResource;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\Responses;
use App\Services\DeepLService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditGuide extends EditRecord
{
    protected static string $resource = GuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('voir')
                ->label('Voir le profil')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => GuideResource::getUrl('view', ['record' => $this->record])),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['guide_type'] = optional($this->record->Guide->first())->pro_local ?? 'local';

        // ── Questions / Réponses ─────────────────────────────────────────────
        $guideId   = DB::table('guides')->where('user_id', $this->record->id)->value('guide_id');
        $questions = Question::where('contexts', 'like', '%guide%')->get();

        $existingChoices = $guideId
            ? DB::table('responses')
                ->join('question_choices', 'responses.choice_id', '=', 'question_choices.id')
                ->where('responses.entity', 'guide')
                ->where('responses.entity_id', $guideId)
                ->select('question_choices.question_id', 'responses.choice_id')
                ->get()
                ->groupBy('question_id')
            : collect();

        foreach ($questions as $question) {
            $data['response_q_' . $question->id] = $existingChoices
                ->get($question->id, collect())
                ->pluck('choice_id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Gestion de la photo de profil
        if (empty($data['profile_path'])) {
            // Aucune nouvelle photo → on garde l'existante
            $data['profile_path'] = $this->record->profile_path;
        } elseif (!str_starts_with($data['profile_path'], 'http')) {
            // Filament renvoie le chemin relatif S3 → on construit la full URL
            $data['profile_path'] = Storage::disk('s3')->url($data['profile_path']);
        }

        // Traduire about_me (FR → EN) si le texte a changé
        $newAboutMe = trim($data['about_me'] ?? '');
        if ($newAboutMe !== '' && $newAboutMe !== $this->record->about_me) {
            try {
                $deepl = app(DeepLService::class);
                $translated = $deepl->translateFROMTO($newAboutMe, 'FR', 'EN');
                if ($translated) {
                    $data['about_me_en'] = $translated;
                }
            } catch (\Throwable $e) {
                Log::warning('EditGuide: traduction about_me échouée — ' . $e->getMessage());
            }
        }

        // Sauvegarder le type de guide (pro/local) dans le modèle Guide
        $guide = $this->record->Guide->first();
        if ($guide && isset($data['guide_type'])) {
            $guide->pro_local = $data['guide_type'];
            $guide->save();
        }
        unset($data['guide_type']);

        // ── Questions / Réponses ─────────────────────────────────────────────
        $guideId  = $guide?->guide_id;
        $userId   = $this->record->id;

        if ($guideId) {
            $questions = Question::where('contexts', 'like', '%guide%')->get();

            foreach ($questions as $question) {
                $fieldName = 'response_q_' . $question->id;
                $choiceIds = array_map('intval', $data[$fieldName] ?? []);
                unset($data[$fieldName]);

                $oldChoiceIds = QuestionChoice::where('question_id', $question->id)->pluck('id');
                Responses::where('entity', 'guide')
                    ->where('entity_id', $guideId)
                    ->whereIn('choice_id', $oldChoiceIds)
                    ->delete();

                foreach ($choiceIds as $choiceId) {
                    Responses::create([
                        'user_id'     => $userId,
                        'choice_id'   => $choiceId,
                        'question_id' => $question->id,
                        'entity'      => 'guide',
                        'entity_id'   => $guideId,
                    ]);
                }
            }
        } else {
            // Pas de guide_id → nettoyer les champs hors modèle
            foreach (array_keys($data) as $key) {
                if (str_starts_with($key, 'response_q_')) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Guide mis à jour');
    }
}
