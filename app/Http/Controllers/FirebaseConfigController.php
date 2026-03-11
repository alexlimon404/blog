<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class FirebaseConfigController extends Controller
{
    public function config(): JsonResponse
    {
        return response()->json([
            'apiKey' => config('services.firebase.api_key'),
            'authDomain' => config('services.firebase.auth_domain'),
            'projectId' => config('services.firebase.project_id'),
            'storageBucket' => config('services.firebase.storage_bucket'),
            'messagingSenderId' => config('services.firebase.messaging_sender_id'),
            'appId' => config('services.firebase.app_id'),
        ]);
    }
}
