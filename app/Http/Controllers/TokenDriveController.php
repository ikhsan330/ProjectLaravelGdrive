<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokenDriveController extends Controller
{
    public function token()
    {
        $client_id = config('services.google.client_id');
        $client_secret = config('services.google.client_secret');
        $refresh_token = config('services.google.refresh_token');

        try {
            $response = Http::post('https://oauth2.googleapis.com/token', [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token',
            ]);

            Log::info('Google token response: ' . $response->body());
            $response->throw();
            $accessToken = $response->json('access_token');
            $expiresIn = $response->json('expires_in');
            cache()->put('google_drive_access_token', $accessToken, now()->addSeconds($expiresIn - 300));
            return $accessToken;
        } catch (\Exception $e) {
            return null;
        }
    }


}
