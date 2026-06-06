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

        $fechasPorGestion = [
            '1-2025' => ['2025-01-02', '2025-02-16'],
            '2-2025' => ['2025-06-02', '2025-07-20'],
            '1-2026' => ['2026-01-05', '2026-02-15'],
            '2-2026' => ['2026-06-03', '2026-07-20'],
        ];

        $grupoGestion = DB::table('grupo')->pluck('codigo_gestion', 'id')->toArray();

        // Gestiones cerradas — postulantes con estado aprobado o reprobado
        $postulantesGestionesCerradas = DB::table('postulante')
            ->whereIn('estado', ['aprobado', 'reprobado', 'inscrito'])
            ->whereNotNull('id_grupo')
            ->whereIn('id_grupo', DB::table('grupo')
                ->whereIn('codigo_gestion', ['1-2025', '2-2025', '1-2026'])
                ->pluck('id')
            )
            ->get();

        foreach ($postulantesGestionesCerradas as $p) {
            $gestionCodigo = $grupoGestion[$p->id_grupo] ?? '1-2025';
            $fechas = $fechasPorGestion[$gestionCodigo];
            if ($p->estado === 'aprobado') {
                for ($nro = 1; $nro <= 3; $nro++) {
                    foreach ($materias as $idMateria) {  // ← una nota por materia
                        $examenes[] = [
                            'codigo_postulante' => $p->codigo,
                            'nro_examen'        => $nro,
                            'ponderacion'       => $nro == 3 ? 40 : 30,
                            'nota'              => $faker->numberBetween(60, 100),
                            'fecha'             => $faker->dateTimeBetween($fechas[0], $fechas[1])->format('Y-m-d'),
                            'id_materia'        => $idMateria,
                        ];
                    }
                }
            } elseif ($p->estado === 'reprobado') {
                $cantExamenes = $faker->numberBetween(1, 3);
                $materiaReprobada = $faker->randomElement($materias);
                for ($nro = 1; $nro <= $cantExamenes; $nro++) {
                    foreach ($materias as $idMateria) {
                        $nota = $idMateria === $materiaReprobada
                            ? $faker->numberBetween(0, 59)
                            : $faker->numberBetween(60, 100);
                        $examenes[] = [
                            'codigo_postulante' => $p->codigo,
                            'nro_examen'        => $nro,
                            'ponderacion'       => $nro == 3 ? 40 : 30,
                            'nota'              => $nota,
                            'fecha'             => $faker->dateTimeBetween($fechas[0], $fechas[1])->format('Y-m-d'),
                            'id_materia'        => $idMateria,
                        ];
                    }
                }
            } elseif ($p->estado === 'inscrito') {
                foreach ($materias as $idMateria) {
                    $examenes[] = [
                        'codigo_postulante' => $p->codigo,
                        'nro_examen'        => 1,
                        'ponderacion'       => 30,
                        'nota'              => $faker->numberBetween(40, 85),
                        'fecha'             => $faker->dateTimeBetween($fechas[0], $fechas[1])->format('Y-m-d'),
                        'id_materia'        => $idMateria,
                    ];
                }
            }
        }

        // Gestión activa 2-2026 — solo inscritos pueden tener primer parcial (50% chance)
        $postulantes2026 = DB::table('postulante')
            ->where('estado', 'inscrito')
            ->whereIn('id_grupo', DB::table('grupo')
                ->where('codigo_gestion', '2-2026')
                ->pluck('id')
            )
            ->get();

        foreach ($postulantes2026 as $p) {
            $gestionCodigo = $grupoGestion[$p->id_grupo] ?? '1-2025';
            $fechas = $fechasPorGestion[$gestionCodigo];
            if ($faker->boolean(50)) {
                foreach ($materias as $idMateria) {
                    $examenes[] = [
                        'codigo_postulante' => $p->codigo,
                        'nro_examen'        => 1,
                        'ponderacion'       => 30,
                        'nota'              => $faker->numberBetween(30, 95),
                        'fecha'             => $faker->dateTimeBetween($fechas[0], $fechas[1])->format('Y-m-d'),
                        'id_materia'        => $idMateria,
                    ];
                }
            }
        }

        foreach (array_chunk($examenes, 500) as $chunk) {
            DB::table('examen')->insertOrIgnore($chunk);
        }
    }
}