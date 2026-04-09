<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       User::create([
            'name' => 'Administrateur',
            'prenom' => 'System',
            'email' => 'admin@gmail.com',
            'contact' => '0707070707',
            'adresse' => 'Cote d\'Ivoire, Abidjan',
            'role' => 'admin',
            'email_verified_at' => now(),
            'profile_picture' => null,
            'password' => Hash::make('KKStechnologies2022@'),
        ]);
       
    }
}
