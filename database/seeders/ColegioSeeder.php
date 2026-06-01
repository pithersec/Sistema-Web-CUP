<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColegioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('colegio')->insert([
            [
                'id' => 1,
                'cie' => '81980011',
                'nombre' => 'Colegio Nacional Florida',
                'tipo' => 'fiscal',
                'turno' => 'mañana',
                'pais' => 'Bolivia',
                'departamento' => 'Santa Cruz',
                'provincia' => 'Andrés Ibáñez'
            ],
            [
                'id' => 2,
                'cie' => '81980022',
                'nombre' => 'Colegio Marista',
                'tipo' => 'particular',
                'turno' => 'mañana',
                'pais' => 'Bolivia',
                'departamento' => 'Santa Cruz',
                'provincia' => 'Andrés Ibáñez'
            ],
        ]);
    }
}