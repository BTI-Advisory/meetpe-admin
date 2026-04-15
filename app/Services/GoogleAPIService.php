<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAPIService
{

    public function GetLatLang($address)
    {
         //traiter les informations de géolocalisation pour l'expérience avec google api
           
            // Tenter de récupérer les coordonnées depuis le cache
            $location = Cache::remember('geocode_' . $address, now()->addHours(24), function () use ($address) {

                Log::channel('googleAPI')->info('-------------------------LOCATION GOOGLE API- START---------------------');
            
                // Si non en cache

                // Appeler l'API Google Geocoding
                $apiKey = config('services.google.api_key');
                $url = config('services.google.google_maps_url')."/geocode/json?address=" . urlencode($address) . "&key=" . $apiKey;
                Log::channel('googleAPI')->info('URL : '.$url);
                $response = Http::get($url);
                // Vérifier si la réponse est OK et retourner les données nécessaires            
                if ($response->ok()) {
                    $data = $response->json();

                    if (!empty($data['results'])) {
                        Log::channel('googleAPI')->info('URL : '. json_encode( $data['results']));
                        return $data['results'][0]['geometry']['location'];
                    }
                }
                return null; // Retourner null si l'API échoue
            });

            if($location)
            {
                Log::channel('googleAPI')->info('LOCATION OK : '.json_encode($location));
                Log::channel('googleAPI')->info('-------------------------LOCATION GOOGLE API- END---------------------');
                return $location;
            }

    }


     public function GetVillePays($lat, $lang)
    {
         //traiter les informations de géolocalisation pour l'expérience avec google api
           
                Log::channel('googleAPI')->info('-------------------------VILLE-PAYS GOOGLE API- START---------------------');
            
                // Appeler l'API Google Geocoding
                $apiKey = config('services.google.api_key');
                $url = config('services.google.google_maps_url')."/geocode/json?latlng=$lat,$lang&key=$apiKey";
                Log::channel('googleAPI')->info('URL : '.$url);
                $response = Http::get($url);
                // Vérifier si la réponse est OK et retourner les données nécessaires            
                if ($response->ok()) {
                    $data = $response->json();
                    $result = [
                            'city' => null,
                            'country' => null,
                        ];
                    foreach ($data['results'][0]['address_components'] as $component) {
                        if (in_array('locality', $component['types'])) {
                            $result['ville'] = $component['long_name'];
                        }
                        if (in_array('country', $component['types'])) {
                            $result['pays'] = $component['long_name'];
                        }
                    }
                    return $result;
                }
                return null; // Retourner null si l'API échoue

            

    }

    function getTimezoneFromLatLng($lat, $lng)
    {
        $timestamp = now()->timestamp;

        $response = Http::get(config('services.google.google_maps_url')."/timezone/json", [
            'location' => "{$lat},{$lng}",
            'timestamp' => $timestamp,
            'key' => config('services.google.api_key'),
        ]);

        if ($response->successful() && $response['status'] === 'OK') {
            return $response['timeZoneId']; // ex: "Europe/Paris"
        }
        return config('app.timezone', 'UTC');
    }
}
