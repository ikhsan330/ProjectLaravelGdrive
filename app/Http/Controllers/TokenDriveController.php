<?php


namespace App\Http\Controllers;

use App\Models\GoogleSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokenDriveController extends Controller
{
    public function token()
    {
        // 1. Ambil pengaturan dari database
        $settings = GoogleSetting::first();

        // 2. Cek apakah pengaturan sudah ada
        if (!$settings || !$settings->client_id || !$settings->client_secret || !$settings->refresh_token) {
            Log::error('Kredensial Google Drive belum diatur di database.');
            return null; // Atau berikan respons error yang sesuai
        }

        try {
            // 3. Gunakan data dari database
            $response = Http::post('https://oauth2.googleapis.com/token', [
                'client_id' => $settings->client_id,
                'client_secret' => $settings->client_secret,
                'refresh_token' => $settings->refresh_token,
                'grant_type' => 'refresh_token',
            ]);

            $response->throw(); // Lemparkan exception jika request gagal

            $accessToken = $response->json('access_token');
            $expiresIn = $response->json('expires_in');

            // Simpan access token ke cache
            cache()->put('google_drive_access_token', $accessToken, now()->addSeconds($expiresIn - 300));

            return $accessToken;

        } catch (\Exception $e) {
            Log::error('Gagal mendapatkan Google Drive access token: ' . $e->getMessage());
            return null;
        }
    }
}
