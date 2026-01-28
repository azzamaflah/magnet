<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ UPDATE OR CREATE - Jika sudah ada, akan update. Jika belum ada, akan create
        User::updateOrCreate(
            ['email' => 'admin@bpsbantul.com'], // Cari berdasarkan email
            [
                'name' => 'Admin BPS Bantul',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@bpsbantul.com'],
            [
                'name' => 'User Magang',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );
    }
}