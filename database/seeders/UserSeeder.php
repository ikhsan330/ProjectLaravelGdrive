<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'PAK AFIS',
            'email' => 'afis123@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'dosen'
        ]);

        User::create([
            'name' => 'PAK NASSIR',
            'email' => 'nassir123@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'dosen'
        ]);
    }
}
