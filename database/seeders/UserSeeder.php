<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. ADMIN
        User::create([
            'name' => 'Administrateur',
            'prenom' => 'System',
            'email' => 'admin@gmail.com',
            'contact' => '0707070707',
            'adresse' => 'Abidjan, Cocody',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('KKStechnologies2022@'),
        ]);

        // 2. POLICE (Service)
        $police = User::create([
            'name' => 'Commissariat du 22ème Arrondissement',
            'prenom' => 'Angré',
            'email' => 'police@gmail.com',
            'contact' => '0101010101',
            'adresse' => 'Abidjan, Angré Nouveau Bureau',
            'role' => 'police',
            'latitude' => 5.39846040,
            'longitude' => -3.99131950,
            'email_verified_at' => now(),
            'password' => Hash::make('azertyui'),
        ]);

        // 3. GENDARMERIE (Service)
        User::create([
            'name' => 'Brigade de Gendarmerie',
            'prenom' => 'Cocody',
            'email' => 'gendarmerie@gmail.com',
            'contact' => '0202020202',
            'adresse' => 'Abidjan, Cocody Plateau',
            'role' => 'gendarmerie',
            'latitude' => 5.3484, // Coordonnées indicatives pour la gendarmerie
            'longitude' => -4.0125,
            'email_verified_at' => now(),
            'password' => Hash::make('azertyui'),
        ]);

        // 4. ASSURANCE (Compagnie)
        User::create([
            'name' => 'AMSA Assurances',
            'prenom' => 'Direction',
            'email' => 'assurance@gmail.com',
            'contact' => '0303030303',
            'adresse' => 'Plateau, Abidjan',
            'role' => 'assurance',
            'email_verified_at' => now(),
            'password' => Hash::make('azertyui'),
        ]);

        // 5. AGENT (Rattaché à la police ci-dessus)
        User::create([
            'name' => 'Kouassi',
            'prenom' => 'Jean',
            'email' => 'agent@gmail.com',
            'contact' => '0404040404',
            'adresse' => 'Angré, Abidjan',
            'role' => 'agent',
            'service_id' => $police->id,
            'email_verified_at' => now(),
            'password' => Hash::make('azertyui'),
        ]);

        // 6. ASSURÉ (Client)
        User::create([
            'name' => 'Konan',
            'prenom' => 'Bertin',
            'email' => 'assure@gmail.com',
            'contact' => '0798278981',
            'adresse' => 'Riviera, Abidjan',
            'role' => 'assure',
            'code_user' => 'CM-ILGYKR-2026',
            'email_verified_at' => now(),
            'password' => Hash::make('azertyui'),
        ]);
    }
}
