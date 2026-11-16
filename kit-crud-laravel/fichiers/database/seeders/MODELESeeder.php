<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MODELE;

class MODELESeeder extends Seeder
{
    public function run(): void
    {
        MODELE::factory()->count(20)->create();
    }
}
