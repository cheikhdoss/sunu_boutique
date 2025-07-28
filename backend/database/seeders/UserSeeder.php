<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Créer un utilisateur admin
        User::create([
            'name' => 'Admin Sunu Boutique',
            'email' => 'admin@sunuboutique.sn',
            'password' => Hash::make('admin123'),
            'phone' => '+221 77 123 45 67',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        // Créer un utilisateur client
        User::create([
            'name' => 'Client Test',
            'email' => 'client@sunuboutique.sn',
            'password' => Hash::make('password123'),
            'phone' => '+221 77 987 65 43',
            'date_of_birth' => '1995-05-15',
            'gender' => 'female',
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);

        // Créer quelques utilisateurs supplémentaires
        User::factory(10)->create();
    }
}