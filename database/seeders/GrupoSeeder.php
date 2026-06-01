<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('grupo')->insert([
            // Grupos Históricos 1-2025
            ['id' => 1, 'aula' => '236-1', 'turno' => 'mañana', 'horario' => '07:00 - 09:15', 'total_ins' => 45, 'codigo_gestion' => '1-2025'],
            ['id' => 2, 'aula' => '236-2', 'turno' => 'tarde', 'horario' => '14:00 - 16:15', 'total_ins' => 40, 'codigo_gestion' => '1-2025'],
            
            // Grupos Históricos 2-2025
            ['id' => 3, 'aula' => '236-1', 'turno' => 'mañana', 'horario' => '07:00 - 09:15', 'total_ins' => 35, 'codigo_gestion' => '2-2025'],
            
            // Grupos Actuales 1-2026
            ['id' => 4, 'aula' => 'LAB-01', 'turno' => 'mañana', 'horario' => '07:00 - 09:15', 'total_ins' => 55, 'codigo_gestion' => '1-2026'],
            ['id' => 5, 'aula' => 'LAB-02', 'turno' => 'tarde', 'horario' => '14:00 - 16:15', 'total_ins' => 50, 'codigo_gestion' => '1-2026'],
            ['id' => 6, 'aula' => '236-3', 'turno' => 'noche', 'horario' => '18:15 - 20:30', 'total_ins' => 30, 'codigo_gestion' => '1-2026'],
        ]);
    }
}