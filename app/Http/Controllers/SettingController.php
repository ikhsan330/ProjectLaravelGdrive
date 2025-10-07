<?php

namespace App\Http\Controllers;

use App\Models\GoogleSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Menampilkan form untuk pengaturan Google Drive.
     */
    public function edit()
    {
        // Ambil data pertama dari tabel, atau buat instance baru jika kosong
        $settings = GoogleSetting::firstOrNew([]);
        return view('admin.settings.google-drive', compact('settings'));
    }


    public function update(Request $request)
    {
        // 1. Validasi data
        $request->validate([
            // Google Settings
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'refresh_token' => 'required|string',
            'mail_username' => 'nullable|email',
            'mail_password' => 'nullable|string|min:6', // Hanya validasi jika diisi
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'nullable|email',
        ]);

        // 2. Ambil semua data kecuali password jika kosong
        $data = $request->except('mail_password');

        // 3. Hanya perbarui password jika field diisi
        if ($request->filled('mail_password')) {
            $data['mail_password'] = $request->mail_password;
        }

        // 4. Gunakan updateOrCreate untuk menyimpan semua data
        GoogleSetting::updateOrCreate(
            ['id' => 1],
            $data
        );

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}
