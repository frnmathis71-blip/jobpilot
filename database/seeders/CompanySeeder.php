<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            'Tecnomat',
            'Castorama',
            'Carrefour',
            'Auchan',
        ];

        foreach ($companies as $name) {
            Company::firstOrCreate([
                'name' => $name,
            ]);
        }
    }
}