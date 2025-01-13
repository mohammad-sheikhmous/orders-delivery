<?php


namespace App\Services;

use GuzzleHttp\Client;

class FcmService
{
    protected $client;
    protected $serverKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->serverKey = env('FCM_SERVER_KEY');
    }

    public function sendNotification($tokens, $title, $body, $data = [])
    {
        $url = 'https://fcm.googleapis.com/v1/projects/order-delivery-8edd5/messages:send';
        $headers = [
            'Authorization' => 'key=' . $this->serverKey,
            'Content-Type' => 'application/json',
        ];

        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => $data,
        ];

        try {
            $response = $this->client->post($url, [
                'headers' => $headers,
                'json' => $payload,
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    public function FCM($fcm_id, $notification)
    {
        $fcmUrl = 'https://fcm.googleapis.com/v1/projects/order-delivery-8edd5/messages:send';
        $token = is_array($fcm_id) ? $fcm_id : array($fcm_id);
        $bearer_token = self::getGoogleAccessToken();
        foreach ($token as $item) {
            $fcmNotification = [
                'message' => [
                    "token" => $item,
                    "data" => $notification,
                    'notification' => [
                        "title" => $notification['title'],
                        "body" => $notification['message'],
                    ],
                ]
            ];
            $headers = [
                'Authorization: Bearer ' . $bearer_token,
                'Content-Type: application/json'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fcmUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
            $result = curl_exec($ch);
            curl_close($ch);

            $resultData = json_decode($result, true);
            \Log::info('Fcm notification send fcm_id = ' . $fcm_id . " ::::", [$resultData]);
        }
    }

    public function getGoogleAccessToken()
    {
        $credentialsFilePath = base_path(env('FIREBASE_CREDENTIALS'));
        $client = new \Google_Client();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        return $token['access_token'];
    }
}
