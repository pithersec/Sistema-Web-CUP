<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ExamenSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_BO');
        $materias = [1, 2, 3, 4];
        $examenes = [];

        // Gestiones cerradas — postulantes con estado aprobado o reprobado
        $postulantesGestionesCerradas = DB::table('postulante')
            ->whereIn('estado', ['aprobado', 'reprobado', 'inscrito'])
            ->whereNotNull('id_grupo')
            ->whereIn('id_grupo', DB::table('grupo')
                ->whereIn('codigo_gestion', ['1-2025', '2-2025'])
                ->pluck('id')
            )
            ->get();

        foreach ($postulantesGestionesCerradas as $p) {
            if ($p->estado === 'aprobado') {
                // 3 exámenes todos >= 60
                for ($nro = 1; $nro <= 3; $nro++) {
                    $examenes[] = [
                        'codigo_postulante' => $p->codigo,
                        'nro_examen'        => $nro,
                        'ponderacion'       => $nro == 3 ? 40.00 : 30.00,
                        'nota'              => $faker->randomFloat(2, 60, 100),
                        'fecha'             => $faker->dateTimeBetween('2025-02-01', '2025-06-30')->format('Y-m-d'),
                        'id_materia'        => $faker->randomElement($materias),
                    ];
                }
            } elseif ($p->estado === 'reprobado') {
                // 1-3 exámenes, al menos uno < 60
                $cantExamenes = $faker->numberBetween(1, 3);
                $examenReprobado = $faker->numberBetween(1, $cantExamenes);
                for ($nro = 1; $nro <= $cantExamenes; $nro++) {
                    $nota = $nro === $examenReprobado
                        ? $faker->randomFloat(2, 0, 59)
                        : $faker->randomFloat(2, 60, 100);
                    $examenes[] = [
                        'codigo_postulante' => $p->codigo,
                        'nro_examen'        => $nro,
                        'ponderacion'       => $nro == 3 ? 40.00 : 30.00,
                        'nota'              => $nota,
                        'fecha'             => $faker->dateTimeBetween('2025-02-01', '2025-06-30')->format('Y-m-d'),
                        'id_materia'        => $faker->randomElement($materias),
                    ];
                }
            } elseif ($p->estado === 'inscrito') {
                // Solo el primer parcial
                $examenes[] = [
                    'codigo_postulante' => $p->codigo,
                    'nro_examen'        => 1,
                    'ponderacion'       => 30.00,
                    'nota'              => $faker->randomFloat(2, 40, 85),
                    'fecha'             => $faker->dateTimeBetween('2025-02-01', '2025-03-30')->format('Y-m-d'),
                    'id_materia'        => $faker->randomElement($materias),
                ];
            }
        }

        // Gestión activa 1-2026 — solo inscritos pueden tener primer parcial (50% chance)
        $postulantes2026 = DB::table('postulante')
            ->where('estado', 'inscrito')
            ->whereIn('id_grupo', DB::table('grupo')
                ->where('codigo_gestion', '1-2026')
                ->pluck('id')
            )
            ->get();

        foreach ($postulantes2026 as $p) {
            if ($faker->boolean(50)) {
                $examenes[] = [
                    'codigo_postulante' => $p->codigo,
                    'nro_examen'        => 1,
                    'ponderacion'       => 30.00,
                    'nota'              => $faker->randomFloat(2, 30, 95),
                    'fecha'             => $faker->dateTimeBetween('2026-02-01', '2026-04-30')->format('Y-m-d'),
                    'id_materia'        => $faker->randomElement($materias),
                ];
            }
        }

        foreach (array_chunk($examenes, 500) as $chunk) {
            DB::table('examen')->insert($chunk);
        }
    }
}