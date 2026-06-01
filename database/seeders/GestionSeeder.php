<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GestionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('gestion')->insert([
            [
                'codigo' => '1-2025',
                'fecha_ini' => '2025-02-01',
                'fecha_fin' => '2025-06-30'
            ],
            [
                'codigo' => '2-2025',
                'fecha_ini' => '2025-08-01',
                'fecha_fin' => '2025-12-31'
            ],
            [
                'codigo' => '1-2026',
                'fecha_ini' => '2026-02-01',
                'fecha_fin' => '2026-06-30'
            ],
        ]);
    }
}
