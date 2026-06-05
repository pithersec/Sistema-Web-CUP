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
                'codigo'    => '1-2025',
                'fecha_ini' => '2025-01-02',
                'fecha_fin' => '2025-02-16'
            ],
            [
                'codigo'    => '2-2025',
                'fecha_ini' => '2025-06-02',
                'fecha_fin' => '2025-07-20'
            ],
            [
                'codigo'    => '1-2026',
                'fecha_ini' => '2026-01-05',
                'fecha_fin' => '2026-02-15'
            ],
            [
                'codigo'    => '2-2026',
                'fecha_ini' => '2026-06-03',
                'fecha_fin' => '2026-07-20'
            ],
        ]);
    }
}
