<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@cumibakar.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => true,
        ]);

        // Create User Biasa
        User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'user@cumibakar.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => User::ROLE_USER,
            'jabatan' => 'Warga',
            'is_active' => true,
        ]);

        // Create default categories
        \App\Models\Kategori::create(['nama' => 'Pemerintahan', 'kode' => 'PEM', 'urutan' => 1]);
        \App\Models\Kategori::create(['nama' => 'Pembangunan', 'kode' => 'PEMB', 'urutan' => 2]);
        \App\Models\Kategori::create(['nama' => 'Kemasyarakatan', 'kode' => 'MASY', 'urutan' => 3]);
    }
}
