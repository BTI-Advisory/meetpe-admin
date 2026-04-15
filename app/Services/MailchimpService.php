<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class MailchimpService
{
    protected $apiKey;
    protected $listId;
    protected $client;

    public function __construct($apiKey, $listId)
    {
        $this->apiKey = $apiKey;
        $this->listId = $listId;
        $this->client = new Client([
            'base_uri' => 'https://' . substr($apiKey, strpos($apiKey, '-') + 1) . '.api.mailchimp.com/3.0/',
            'auth' => ['apikey', $apiKey]
        ]);
    }

    public function sendEmailToUser($email)
    {
      try {
        $response = $this->client->post('campaigns', [
            'json' => [
                'recipients' => [
                    'list_id' => $this->listId,
                    'to_email' => $email
                ],
                'type' => 'regular',
                'settings' => [
                    'subject_line' => 'Your Subject Line',
                    'reply_to' => 'your_email@example.com',
                    'from_name' => 'Your Name',
                    'template' => [
                        'id' => 12345, // Your template ID
                        'sections' => []
                    ]
                ]
            ]
        ]);
      } catch (ClientException $th) {
        //throw $th;
        dd($th->getMessage());
      }

        return $response->getBody();
    }
}
