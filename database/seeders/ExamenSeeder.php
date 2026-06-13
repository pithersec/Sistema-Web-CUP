<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ExamenSeeder extends Seeder
{
    public function run(): void
    {
        $faker    = Faker::create('es_BO');
        $materias = [1, 2, 3, 4];
        $examenes = [];

        $fechasPorGestion = [
            '1-2025' => ['2025-01-02', '2025-02-16'],
            '2-2025' => ['2025-06-02', '2025-07-20'],
            '1-2026' => ['2026-01-05', '2026-02-15'],
            '2-2026' => ['2026-06-03', '2026-07-20'],
        ];

        $grupoGestion = DB::table('grupo')->pluck('codigo_gestion', 'id')->toArray();

        // ---------------------------------------------------------------
        // Gestiones cerradas: aprobados, reprobados e inscritos con grupo
        // ---------------------------------------------------------------
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
            $fechas        = $fechasPorGestion[$gestionCodigo];

            if ($p->estado === 'aprobado') {
                // Aprobados: 3 exámenes, notas >= 60 en todas las materias
                // La nota final por materia = N1×30% + N2×30% + N3×40% >= 60
                for ($nro = 1; $nro <= 3; $nro++) {
                    foreach ($materias as $idMateria) {
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
                // Reprobados: 85% rinden los 3 exámenes, 15% solo 1 o 2
                $cantExamenes    = $faker->boolean(85) ? 3 : $faker->numberBetween(1, 2);
                $materiaReprobada = $faker->randomElement($materias);

                for ($nro = 1; $nro <= $cantExamenes; $nro++) {
                    foreach ($materias as $idMateria) {
                        // La materia reprobada tiene nota < 60, las demás >= 60
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
                // Inscritos en gestiones cerradas: solo tienen el primer examen
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

        // ---------------------------------------------------------------
        // Gestión activa 2-2026: inscritos con grupo, 50% chance de tener N1
        // ---------------------------------------------------------------
        $postulantes2026 = DB::table('postulante')
            ->where('estado', 'inscrito')
            ->whereIn('id_grupo', DB::table('grupo')
                ->where('codigo_gestion', '2-2026')
                ->pluck('id')
            )
            ->get();

        foreach ($postulantes2026 as $p) {
            $fechas = $fechasPorGestion['2-2026'];
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

        foreach (array_chunk($examenes, 500) as $chunk)
            DB::table('examen')->insertOrIgnore($chunk);

        // ---------------------------------------------------------------
        // POST-PROCESO: asignar carrera en postulante_carrera para aprobados
        // Orden: mayor promedio general primero (N1×30% + N2×30% + N3×40%)
        // Por gestión cerrada, verificando cupos reales en carrera_gestion
        // ---------------------------------------------------------------
        $cuposDisponibles = [];
        $carreraGestions  = DB::table('carrera_gestion')->get();
        foreach ($carreraGestions as $cg) {
            $key = $cg->codigo_carrera . '|' . $cg->plan_carrera . '|' . $cg->modalidad_carrera;
            $cuposDisponibles[$cg->codigo_gestion][$key] = $cg->cupos;
        }

        // Calcular promedio general por aprobado (promedio de notas finales por materia)
        // Nota final materia = SUM(nota * ponderacion) / 100
        $promedios = DB::table('examen')
            ->join('postulante', 'examen.codigo_postulante', '=', 'postulante.codigo')
            ->whereIn('postulante.estado', ['aprobado'])
            ->whereIn('postulante.gestion_grupo', ['1-2025', '2-2025', '1-2026'])
            ->select(
                'examen.codigo_postulante',
                'postulante.gestion_grupo',
                DB::raw('AVG(examen.nota * examen.ponderacion / 100.0) as promedio_final')
            )
            ->groupBy('examen.codigo_postulante', 'postulante.gestion_grupo')
            ->orderByDesc('promedio_final')
            ->get();

        // Obtener opciones de carrera de cada aprobado
        $opcionesPorPostulante = DB::table('postulante_carrera')
            ->whereIn('codigo_postulante', $promedios->pluck('codigo_postulante'))
            ->get()
            ->groupBy('codigo_postulante');

        foreach ($promedios as $p) {
            $gestion  = $p->gestion_grupo;
            $opciones = $opcionesPorPostulante[$p->codigo_postulante] ?? collect();

            $asignada     = null;
            $planAsignado = null;
            $modAsignada  = null;
            $opcionUsada  = null;

            foreach ([1, 2] as $opcion) {
                $op = $opciones->firstWhere('opcion', $opcion);
                if (!$op) continue;
                $key = $op->codigo_carrera . '|' . $op->plan_carrera . '|' . $op->modalidad_carrera;

                if (isset($cuposDisponibles[$gestion][$key]) && $cuposDisponibles[$gestion][$key] > 0) {
                    $cuposDisponibles[$gestion][$key]--;
                    $asignada     = $op->codigo_carrera;
                    $planAsignado = $op->plan_carrera;
                    $modAsignada  = $op->modalidad_carrera;
                    $opcionUsada  = $opcion;
                    break;
                }
            }

            if ($asignada) {
                // Marcar la opción asignada como true
                DB::table('postulante_carrera')
                    ->where('codigo_postulante', $p->codigo_postulante)
                    ->where('codigo_carrera', $asignada)
                    ->where('plan_carrera', $planAsignado)
                    ->where('modalidad_carrera', $modAsignada)
                    ->update(['asignada' => true]);
            } else {
                // Lista de espera: insertar fila con opcion null y asignada true
                // Carrera con más cupos disponibles en esta gestión
                $mejorKey   = null;
                $mejorCupos = -1;
                foreach (($cuposDisponibles[$gestion] ?? []) as $key => $cupos) {
                    if ($cupos > $mejorCupos) {
                        $mejorCupos = $cupos;
                        $mejorKey   = $key;
                    }
                }

                if ($mejorKey && $mejorCupos > 0) {
                    [$codigoCarrera, $plan, $modalidad] = explode('|', $mejorKey);
                    $cuposDisponibles[$gestion][$mejorKey]--;
                    DB::table('postulante_carrera')->insert([
                        'codigo_postulante' => $p->codigo_postulante,
                        'codigo_carrera'    => $codigoCarrera,
                        'plan_carrera'      => $plan,
                        'modalidad_carrera' => $modalidad,
                        'opcion'            => null,
                        'asignada'          => true,
                    ]);
                }
            }
        }
    }
}