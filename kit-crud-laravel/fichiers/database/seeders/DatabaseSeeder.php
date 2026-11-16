<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // L'ordre compte : le PARENT d'abord, l'ENFANT ensuite
        $this->call([
            PARENT_MODELESeeder::class,
            MODELESeeder::class,
        ]);
    }
}
