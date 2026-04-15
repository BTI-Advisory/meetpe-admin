<?php
namespace App\Services;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class SubscribeUserToMailchimp {
    public string $key;
    public string $listId;
    public const BASE_URL = "https://us18.api.mailchimp.com/3.0/";

    public function __construct()
    {
        $this->key    = config('services.mailchimp.key');
        $this->listId = config('services.mailchimp.list_id');
    }

    public function handle(): JsonResponse
    {
        try {
            $response = Http::acceptJson()
                ->withToken($this->key)
                ->post(self::BASE_URL . 'lists/' . $this->listId . '/members', [
                    'status' => 'subscribed',
                    'email_address' => "zakaria.newayesoft@gmail.com",//  $event->user->email
                    'merge_fields' => [
                       // 'FNAME' => $event->user->firstname, 'LNAME' => $event->user->lastname,
                        'FNAME' => "ahmed", 'LNAME' => "lamiaa",
                        'ADDRESS' => [
                            'addr1' => "add",
                        //    'addr1' => $event->user->address,
                            'city' => 'n/a',
                            'state' => 'n/a',
                            'zip' => 'n/a',
                            'country' => 'n/a'
                        ], 'PHONE' => "+212698998804"
                      //  ], 'PHONE' => $event->user->phone
                    ],
                ]);

            $response->throw();

            $responseData = $response->body();
            $decodedResponse = json_decode($responseData, true);

            if ($response->status() == Response::HTTP_OK) {
                return response()->json([
                    'success' => true,
                    'message' => 'member successfully added to mailchimp list',
                    'responseBody' => $decodedResponse,
                ]);
            }
        } catch (Exception $e) {
            //dd($e->getMessage());
           //  print_r($e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
