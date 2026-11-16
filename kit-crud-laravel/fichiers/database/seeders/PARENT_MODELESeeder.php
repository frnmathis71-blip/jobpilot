<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PARENT_MODELE;

class PARENT_MODELESeeder extends Seeder
{
    public function run(): void
    {
        PARENT_MODELE::factory()->count(5)->create();
    }
}
