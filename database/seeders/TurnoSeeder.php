<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('turno')->insert([
            ['nombre' => 'mañana', 'hora_inicio' => '07:00', 'hora_fin' => '11:00'],
            ['nombre' => 'tarde',  'hora_inicio' => '13:00', 'hora_fin' => '17:00'],
            ['nombre' => 'noche',  'hora_inicio' => '18:00', 'hora_fin' => '22:00'],
        ]);
    }
}