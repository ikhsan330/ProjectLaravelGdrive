<?php

namespace App\Providers;

use App\Models\GoogleSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application  services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot(): void
    {
        // Cek jika tabel 'google_settings' sudah ada
        if (Schema::hasTable('google_settings')) {
            try {
                $settings = GoogleSetting::first();

                if ($settings) { // Pastikan settings tidak null
                    // Konfigurasi Email Dinamis
                    if ($settings->mail_username && $settings->mail_password) {
                        Config::set('mail.mailers.smtp.host', 'smtp.gmail.com'); // Atau bisa ditambahkan ke form juga
                        Config::set('mail.mailers.smtp.port', 587); // Atau bisa ditambahkan ke form juga
                        Config::set('mail.mailers.smtp.encryption', $settings->mail_encryption);
                        Config::set('mail.mailers.smtp.username', $settings->mail_username);
                        Config::set('mail.mailers.smtp.password', $settings->mail_password); // Model sudah otomatis decrypt
                        Config::set('mail.from.address', $settings->mail_from_address);
                        Config::set('mail.from.name', config('app.name'));
                    }
                }
            } catch (\Exception $e) {
                // Biarkan saja, mungkin database belum siap saat migrasi
                // atau kolom password masih kosong dan belum bisa di-decrypt
            }
        }
    }
}
