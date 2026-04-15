<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepLService
{
    public function translateFROMTO(string $text, ?string $sourceLang = "FR", string $targetLang = "EN"): ?string
    {
        if (empty(trim($text))) return null;

        $response = Http::withHeaders([
            'Authorization' => 'DeepL-Auth-Key ' . env('DEEPL_API_KEY'),
        ])->asForm()->post(env('DEEPL_API_URL'), [
            'text'        => $text,
            'source_lang' => $sourceLang,
            'target_lang' => $targetLang,
        ]);

        return $response->successful()
            ? $response['translations'][0]['text']
            : null;
    }
}
