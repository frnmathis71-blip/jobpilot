<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 3 comptes de test (mot de passe : password)
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Gestionnaire',
            'email' => 'ROLE@example.com',
            'role' => 'ROLE',
        ]);

        User::factory()->create([
            'name' => 'Simple utilisateur',
            'email' => 'user@example.com',
            'role' => 'user',
        ]);

        // L'ordre compte : le PARENT d'abord, l'ENFANT ensuite
        $this->call([
            PARENT_MODELESeeder::class,
            MODELESeeder::class,
        ]);
    }
}
