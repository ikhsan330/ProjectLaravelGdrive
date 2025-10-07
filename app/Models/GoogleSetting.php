<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class GoogleSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'client_secret',
        'refresh_token',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
    ];

    protected function mailPassword(): Attribute
    {
        return Attribute::make(
            // Saat mengambil data (decrypt)
            get: fn($value) => $value ? Crypt::decryptString($value) : null,
            // Saat menyimpan data (encrypt)
            set: fn($value) => $value ? Crypt::encryptString($value) : null,
        );
    }
}
