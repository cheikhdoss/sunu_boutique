<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'comedie442@gmail.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Assigner le rôle admin
        $admin->assignRole('admin');
    }
}