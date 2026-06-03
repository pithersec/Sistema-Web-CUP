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
            ['id' => 3, 'cie' => '81980033', 'nombre' => 'Colegio La Salle',         'tipo' => 'particular', 'turno' => 'mañana', 'pais' => 'Bolivia', 'departamento' => 'Santa Cruz', 'provincia' => 'Andrés Ibáñez'],
            ['id' => 4, 'cie' => '81980044', 'nombre' => 'Colegio Alemán',           'tipo' => 'particular', 'turno' => 'mañana', 'pais' => 'Bolivia', 'departamento' => 'Santa Cruz', 'provincia' => 'Andrés Ibáñez'],
            ['id' => 5, 'cie' => '81980055', 'nombre' => 'U.E. Elvira Morales',      'tipo' => 'fiscal',     'turno' => 'tarde',  'pais' => 'Bolivia', 'departamento' => 'Santa Cruz', 'provincia' => 'Andrés Ibáñez'],
            ['id' => 6, 'cie' => '81980066', 'nombre' => 'Colegio San Ignacio',      'tipo' => 'particular', 'turno' => 'mañana', 'pais' => 'Bolivia', 'departamento' => 'Santa Cruz', 'provincia' => 'Andrés Ibáñez'],
            ['id' => 7, 'cie' => '81980077', 'nombre' => 'U.E. 24 de Septiembre',    'tipo' => 'fiscal',     'turno' => 'mañana', 'pais' => 'Bolivia', 'departamento' => 'Santa Cruz', 'provincia' => 'Andrés Ibáñez'],
            ['id' => 8, 'cie' => '81980088', 'nombre' => 'Colegio Montserrat',       'tipo' => 'particular', 'turno' => 'mañana', 'pais' => 'Bolivia', 'departamento' => 'Santa Cruz', 'provincia' => 'Andrés Ibáñez'],
        ]);
    }
}