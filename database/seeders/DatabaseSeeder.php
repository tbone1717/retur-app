<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // <-- Tambahkan baris ini

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat data user dummy
        User::create([
            'name' => 'Admin Retur',
            'email' => 'admin@retur.com',
            'password' => Hash::make('password123'),
        ]);
    }
}