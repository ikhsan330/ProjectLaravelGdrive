<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up(): void
    {
        Schema::create('google_settings', function (Blueprint $table) {
                 $table->id();

            // Pengaturan Google Drive
            $table->text('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->text('refresh_token')->nullable();

            // Pengaturan Email
            $table->string('mail_username')->nullable();
            $table->text('mail_password')->nullable();
            $table->string('mail_encryption')->default('tls'); // Cukup default, tidak perlu nullable jika ada default
            $table->string('mail_from_address')->nullable(); // <-- KOLOM YANG HILANG DITAMBAHKAN

            $table->timestamps(); // <-- SANGAT PENTING UNTUK DITAMBAHKAN
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_settings');
    }
};
