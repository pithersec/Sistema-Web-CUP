<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ReclamoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        $reclamos = [];

        $descripciones = [
            'Solicita revisión manual de la pregunta de vectores en el examen parcial.',
            'El postulante indica que su nota no fue registrada correctamente.',
            'Solicita reconsideración de puntaje en el examen de matemáticas.',
            'El postulante reporta error en la suma de ponderaciones.',
            'Solicita revisión del examen de física, considera que fue calificado incorrectamente.',
            'El postulante indica que no pudo rendir el examen por problemas de salud y solicita fecha alternativa.',
            'Solicita aclaración sobre los criterios de evaluación aplicados.',
            'El postulante reporta que su grupo fue cambiado sin previo aviso.',
            'Solicita revisión de requisitos, indica que entregó todos los documentos.',
            'El postulante indica error en el registro de su carrera de preferencia.',
        ];

        $personal = DB::table('personal')->pluck('registro')->toArray();

        $configuracion = [
            ['gestion' => '1-2025', 'cantidad' => 35, 'fecha_ini' => '2025-01-02', 'fecha_fin' => '2025-02-16'],
            ['gestion' => '2-2025', 'cantidad' => 45, 'fecha_ini' => '2025-06-02', 'fecha_fin' => '2025-07-20'],
            ['gestion' => '1-2026', 'cantidad' => 20, 'fecha_ini' => '2026-01-05', 'fecha_fin' => '2026-02-15'],
        ];

        foreach ($configuracion as $config) {
            $postulantes = DB::table('postulante')
                ->whereIn('id_grupo', DB::table('grupo')
                    ->where('codigo_gestion', $config['gestion'])
                    ->pluck('id')
                )
                ->pluck('codigo')
                ->toArray();

            if (empty($postulantes)) continue;

            $seleccionados = (array) $faker->randomElements($postulantes, min($config['cantidad'], count($postulantes)));

            foreach ($seleccionados as $codigo) {
                $reclamos[] = [
                    'descripcion'       => $faker->randomElement($descripciones),
                    'fecha'             => $faker->dateTimeBetween($config['fecha_ini'], $config['fecha_fin'])->format('Y-m-d H:i:s'),
                    'dirigido'          => $faker->randomElement(['Jefatura de Admisión FICCT', 'Coordinación Académica', 'Dirección FICCT']),
                    'estado'            => $faker->randomElement(['pendiente', 'atendido', 'rechazado']),
                    'codigo_postulante' => $codigo,
                    'registro_personal' => $faker->randomElement($personal),
                ];
            }
        }

        DB::table('reclamo')->insert($reclamos);
    }
}