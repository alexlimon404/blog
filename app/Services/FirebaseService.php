<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FirebaseService
{
    public static function sendPushNotification(string $token, string $title, string $body): array
    {
        $accessToken = self::getAccessToken();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/v1/projects/' . config('services.firebase.project_id') . '/messages:send', [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'webpush' => [
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'icon' => asset('favicon.ico'),
                        'badge' => asset('favicon.ico'),
                    ],
                    'fcm_options' => [
                        'link' => url('/'),
                    ]
                ]
            ]
        ]);

        if (! $response->successful()) {
            throw new \Exception('Firebase push notification failed: ' . $response->body());
        }

        return $response->json();
    }

    private static function getAccessToken(): string
    {
        return Cache::remember('firebase_access_token', 3500, function () {
            $credentialsPath = config('services.firebase.credentials_path');

            if (! $credentialsPath || ! Storage::exists($credentialsPath)) {
                throw new \Exception('Firebase service account file not found at: ' . $credentialsPath);
            }

            $credentials = json_decode(Storage::get($credentialsPath), true);

            if (! $credentials) {
                throw new \Exception('Invalid Firebase service account file');
            }

            // Create JWT
            $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
            $now = time();
            $payload = json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]);

            $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

            $signature = '';
            openssl_sign(
                $base64Header . '.' . $base64Payload,
                $signature,
                $credentials['private_key'],
                'SHA256'
            );

            $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;

            // Exchange JWT for access token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if (! $response->successful()) {
                throw new \Exception('Failed to get access token: ' . $response->body());
            }

            $data = $response->json();

            if (! isset($data['access_token'])) {
                throw new \Exception('Access token not found in response');
            }

            return $data['access_token'];
        });
    }
}