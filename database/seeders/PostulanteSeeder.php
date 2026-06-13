<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PostulanteSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        $estadosPorGestion = [
            '1-2025' => ['aprobado' => 47, 'reprobado' => 50, 'preinscrito' => 3],
            '2-2025' => ['aprobado' => 43, 'reprobado' => 54, 'preinscrito' => 3],
            '1-2026' => ['aprobado' => 44, 'reprobado' => 53, 'preinscrito' => 3],
            '2-2026' => ['inscrito' => 85, 'preinscrito' => 15],
        ];

        $gestiones = [
            ['codigo' => '1-2025', 'total' => 847,  'plazo_ini' => '2025-01-02', 'plazo_fin' => '2025-02-16'],
            ['codigo' => '2-2025', 'total' => 1018, 'plazo_ini' => '2025-06-02', 'plazo_fin' => '2025-07-20'],
            ['codigo' => '1-2026', 'total' => 923,  'plazo_ini' => '2026-01-05', 'plazo_fin' => '2026-02-15'],
            ['codigo' => '2-2026', 'total' => 821,  'plazo_ini' => '2026-06-03', 'plazo_fin' => '2026-07-20'],
        ];

        $carreras = [
            ['codigo' => '187', 'plan' => '4', 'modalidad' => 'presencial', 'peso' => 28],
            ['codigo' => '187', 'plan' => '6', 'modalidad' => 'presencial', 'peso' => 21],
            ['codigo' => '187', 'plan' => '5', 'modalidad' => 'presencial', 'peso' => 14],
            ['codigo' => '187', 'plan' => '4', 'modalidad' => 'virtual',    'peso' => 14],
            ['codigo' => '187', 'plan' => '3', 'modalidad' => 'virtual',    'peso' => 14],
            ['codigo' => '323', 'plan' => '0', 'modalidad' => 'presencial', 'peso' => 9],
        ];

        $elegirCarrera = function() use ($carreras, $faker) {
            $total = array_sum(array_column($carreras, 'peso'));
            $rand  = $faker->numberBetween(1, $total);
            $acum  = 0;
            foreach ($carreras as $c) {
                $acum += $c['peso'];
                if ($rand <= $acum) return $c;
            }
            return $carreras[0];
        };

        $departamentos = array_merge(
            array_fill(0, 51, 'Santa Cruz'),
            ['La Paz', 'Cochabamba', 'Oruro', 'Potosí', 'Tarija', 'Beni', 'Pando', 'Chuquisaca', 'Extranjero']
        );

        $nombresPorGenero = [
            'm' => ['Carlos', 'Juan', 'Luis', 'Jorge', 'Miguel', 'Roberto', 'Fernando', 'Alejandro', 'Diego', 'Rodrigo'],
            'f' => ['María', 'Ana', 'Rosa', 'Carmen', 'Patricia', 'Sandra', 'Claudia', 'Gabriela', 'Valentina', 'Daniela'],
        ];

        $apellidos = ['Flores', 'Mamani', 'Quispe', 'Gutierrez', 'Perez', 'Rojas',
                      'Vargas', 'Condori', 'Huanca', 'Torrez', 'Salinas', 'Mendoza',
                      'Choque', 'Quisbert', 'Villca', 'Apaza', 'Lima', 'Cruz'];

        $aulas        = ['236-31', '236-32', '236-33', '236-34', '236-35'];
        $colegios     = [1, 2, 3, 4, 5, 6, 7, 8];
        $turnoPrefijo = ['mañana' => 'M', 'tarde' => 'T', 'noche' => 'N'];
        $turnosOrden  = ['mañana', 'tarde', 'noche'];
        $materias     = DB::table('materia')->pluck('id')->toArray();
        $numMaterias  = count($materias);

        $docentes = DB::table('usuario')
            ->where('id_perfil', 3)
            ->join('personal', 'usuario.registro_personal', '=', 'personal.registro')
            ->join('requisitos_personal', 'personal.registro', '=', 'requisitos_personal.registro_personal')
            ->select('personal.registro', 'requisitos_personal.area')
            ->get()
            ->groupBy('area');

        $areaMateria = [
            1 => 'matematicas',
            2 => 'fisica',
            3 => 'ingles',
            4 => 'computacion',
        ];

        $turnoHoras        = DB::table('turno')->pluck('hora_inicio', 'nombre');
        $materiaDuraciones = DB::table('materia')->pluck('duracion', 'id');

        $usedCis            = [];
        $postulantes        = [];
        $postulantesCarrera = [];
        $datosPersonales    = [];
        $pagos              = [];
        $requisitos         = [];
        $grupos             = [];
        $grupoMaterias      = [];

        $pagoId              = 1;
        $requisitosId        = 1;
        $docenteAsignaciones = [];

        foreach ($gestiones as $gestion) {
            $esActiva       = $gestion['codigo'] === '2-2026';
            $gruposPorTurno = [];

            if (!$esActiva) {
                $pctInscritos   = ($estadosPorGestion[$gestion['codigo']]['aprobado'] ?? 0)
                                + ($estadosPorGestion[$gestion['codigo']]['reprobado'] ?? 0);
                $totalInscritos = (int) round($gestion['total'] * $pctInscritos / 100);

                $numGrupos = (int) ceil($totalInscritos / 70);
                $porTurno  = (int) floor($numGrupos / 3);
                $excedente = $numGrupos % 3;

                $distribucion = [
                    'mañana' => $porTurno + $excedente,
                    'tarde'  => $porTurno,
                    'noche'  => $porTurno,
                ];

                $docenteAsignaciones[$gestion['codigo']] = [];

                foreach ($turnosOrden as $turno) {
                    $prefix          = $turnoPrefijo[$turno];
                    $gruposPorTurno[$turno] = [];
                    $turnoHoraInicio = \Carbon\Carbon::createFromFormat('H:i:s', $turnoHoras[$turno]);

                    for ($i = 1; $i <= $distribucion[$turno]; $i++) {
                        $grupoId  = $prefix . str_pad($i, 3, '0', STR_PAD_LEFT);
                        $grupos[] = [
                            'id'             => $grupoId,
                            'aula'           => $aulas[($i - 1) % count($aulas)],
                            'nombre_turno'   => $turno,
                            'total_ins'      => 0,
                            'codigo_gestion' => $gestion['codigo'],
                        ];

                        $offset    = ($i - 1) % $numMaterias;
                        $acumulado = 0;

                        foreach (range(0, $numMaterias - 1) as $pos) {
                            $idMateria  = $materias[($offset + $pos) % $numMaterias];
                            $duracion   = (float) $materiaDuraciones[$idMateria];
                            $horaInicio = (clone $turnoHoraInicio)->addMinutes((int)($acumulado * 60));
                            $horaFin    = (clone $horaInicio)->addMinutes((int)($duracion * 60));
                            $area       = $areaMateria[$idMateria];
                            $acumulado += $duracion;

                            $registro = null;
                            if (!empty($docentes[$area])) {
                                foreach ($docentes[$area] as $d) {
                                    $asignados = $docenteAsignaciones[$gestion['codigo']][$d->registro] ?? 0;
                                    if ($asignados < 4) {
                                        $registro = $d->registro;
                                        $docenteAsignaciones[$gestion['codigo']][$d->registro] = $asignados + 1;
                                        break;
                                    }
                                }
                            }

                            $grupoMaterias[] = [
                                'id_grupo'          => $grupoId,
                                'gestion_grupo'     => $gestion['codigo'],
                                'id_materia'        => $idMateria,
                                'hora_inicio'       => $horaInicio->format('H:i:s'),
                                'hora_fin'          => $horaFin->format('H:i:s'),
                                'orden'             => $pos + 1,
                                'registro_personal' => $registro,
                            ];
                        }

                        $gruposPorTurno[$turno][] = $grupoId;
                    }
                }
            }

            $estados = $estadosPorGestion[$gestion['codigo']];
            $lista   = [];
            foreach ($estados as $estado => $pct) {
                $cantidad = (int) round($gestion['total'] * $pct / 100);
                for ($i = 0; $i < $cantidad; $i++) $lista[] = $estado;
            }
            while (count($lista) < $gestion['total']) $lista[] = 'preinscrito';
            shuffle($lista);

            $gestionCorta  = str_replace('-', '', $gestion['codigo']);
            $num           = 1;
            $contadorGrupo = [];
            foreach ($gruposPorTurno as $ids) {
                foreach ($ids as $id) $contadorGrupo[$id] = 0;
            }

            foreach ($lista as $estado) {
                do {
                    $ci = (string) $faker->numberBetween(1000000, 9999999);
                } while (in_array($ci, $usedCis));
                $usedCis[] = $ci;
                $codigo = $gestionCorta . str_pad($num++, 4, '0', STR_PAD_LEFT);

                $genero      = $faker->randomElement(['m', 'f']);
                $nombreLower = strtolower($faker->randomElement($nombresPorGenero[$genero]));
                $apellidoLow = strtolower($faker->randomElement($apellidos));
                $numero      = $faker->numberBetween(1, 999);
                $dominio     = $faker->randomElement(['gmail.com', 'gmail.com', 'hotmail.com', 'outlook.com']);
                $formato     = $faker->randomElement([
                    $nombreLower . '.' . $apellidoLow . $numero . $ci,
                    substr($nombreLower, 0, 1) . $apellidoLow . $numero . $ci,
                ]);

                $datosPersonales[] = [
                    'ci'        => $ci,
                    'nombre'    => $faker->randomElement($nombresPorGenero[$genero]),
                    'apellido'  => $faker->randomElement($apellidos),
                    'genero'    => $genero,
                    'telefono'  => '7' . $faker->numerify('#######'),
                    'correo'    => $formato . '@' . $dominio,
                    'fecha_nac' => $faker->dateTimeBetween('-25 years', '-16 years')->format('Y-m-d'),
                    'direccion' => $faker->address(),
                ];

                $tienePago = in_array($estado, ['inscrito', 'aprobado', 'reprobado']);

                if ($tienePago) {
                    $pagos[] = [
                        'id'             => $pagoId,
                        'monto'          => 700.00,
                        'fecha'          => $faker->dateTimeBetween($gestion['plazo_ini'], $gestion['plazo_fin'])->format('Y-m-d H:i:s'),
                        'concepto'       => "Inscripción CUP {$gestion['codigo']}",
                        'estado'         => 'completado',
                        'id_transaccion' => 'pi_' . $faker->bothify('????????????????????????????????'),
                        'moneda'         => 'USD',
                    ];
                }

                $requisitos[] = [
                    'id'               => $requisitosId,
                    'titulo_original'  => $tienePago,
                    'titulo_copia'     => $tienePago,
                    'fotocopia_carnet' => $tienePago,
                    'formulario'       => $tienePago,
                    'comprobante'      => $tienePago,
                    'libreta'          => $tienePago ? true : $faker->boolean(40),
                ];

                if ($esActiva) {
                    $grupoAsignado = null;
                    $gestionGrupo  = null;
                    $turnoGrupo    = $tienePago ? $faker->randomElement($turnosOrden) : null;
                } else {
                    $turnoGrupo    = $faker->randomElement($turnosOrden);
                    $grupoAsignado = null;
                    $gestionGrupo  = null;

                    if ($tienePago && !empty($gruposPorTurno[$turnoGrupo])) {
                        $turnosIntento = array_unique(array_merge(
                            [$turnoGrupo],
                            array_filter($turnosOrden, fn($t) => $t !== $turnoGrupo)
                        ));
                        foreach ($turnosIntento as $t) {
                            foreach (($gruposPorTurno[$t] ?? []) as $gId) {
                                if ($contadorGrupo[$gId] < 70) {
                                    $grupoAsignado = $gId;
                                    $gestionGrupo  = $gestion['codigo'];
                                    $turnoGrupo    = $t;
                                    $contadorGrupo[$gId]++;
                                    break 2;
                                }
                            }
                        }
                    }
                }

                $carrera1 = $elegirCarrera();
                do { $carrera2 = $elegirCarrera(); } while ($carrera2 == $carrera1);

                $postulantes[] = [
                    'codigo'                   => $codigo,
                    'ci'                       => $ci,
                    'procedencia'              => $faker->randomElement($departamentos),
                    'telefono_2'               => $faker->boolean(40) ? '6' . $faker->numerify('#######') : null,
                    'plazo'                    => $faker->dateTimeBetween($gestion['plazo_ini'], $gestion['plazo_fin'])->format('Y-m-d'),
                    'estado'                   => $estado,
                    'gestion_egreso'           => (string) $faker->numberBetween(2021, 2025),
                    'id_requisitos_postulante' => $requisitosId,
                    'id_colegio'               => $faker->randomElement($colegios),
                    'id_pago'                  => $tienePago ? $pagoId : null,
                    'id_grupo'                 => $grupoAsignado,
                    'gestion_grupo'            => $gestionGrupo,
                    'nombre_turno'             => $turnoGrupo,
                    'estado_formulario'        => 'activo',
                    'created_at'               => $faker->dateTimeBetween($gestion['plazo_ini'], $gestion['plazo_fin'])->format('Y-m-d H:i:s'),
                    'updated_at'               => $faker->dateTimeBetween($gestion['plazo_ini'], $gestion['plazo_fin'])->format('Y-m-d H:i:s'),
                ];

                // asignada = false para todos — se actualiza en ExamenSeeder
                $postulantesCarrera[] = ['codigo_postulante' => $codigo, 'codigo_carrera' => $carrera1['codigo'], 'plan_carrera' => $carrera1['plan'], 'modalidad_carrera' => $carrera1['modalidad'], 'opcion' => 1, 'asignada' => false];
                $postulantesCarrera[] = ['codigo_postulante' => $codigo, 'codigo_carrera' => $carrera2['codigo'], 'plan_carrera' => $carrera2['plan'], 'modalidad_carrera' => $carrera2['modalidad'], 'opcion' => 2, 'asignada' => false];

                if ($tienePago) $pagoId++;
                $requisitosId++;
            }

            if (!$esActiva) {
                foreach ($contadorGrupo as $grupoId => $total) {
                    foreach ($grupos as &$g) {
                        if ($g['id'] === $grupoId && $g['codigo_gestion'] === $gestion['codigo']) {
                            $g['total_ins'] = $total;
                            break;
                        }
                    }
                    unset($g);
                }
            }
        }

        foreach (array_chunk($grupos, 500) as $chunk)
            DB::table('grupo')->insert($chunk);

        foreach (array_chunk($grupoMaterias, 500) as $chunk)
            DB::table('grupo_materia')->insert($chunk);

        foreach (array_chunk($datosPersonales, 500) as $chunk)
            DB::table('datos_personales')->insert($chunk);

        foreach (array_chunk($requisitos, 500) as $chunk)
            DB::table('requisitos_postulante')->insert($chunk);

        foreach (array_chunk($pagos, 500) as $chunk)
            DB::table('pago')->insert($chunk);

        foreach (array_chunk($postulantes, 500) as $chunk)
            DB::table('postulante')->insert($chunk);

        foreach (array_chunk($postulantesCarrera, 500) as $chunk)
            DB::table('postulante_carrera')->insert($chunk);

        DB::statement("SELECT setval('requisitos_postulante_id_seq', (SELECT MAX(id) FROM requisitos_postulante))");
        DB::statement("SELECT setval('pago_id_seq', (SELECT MAX(id) FROM pago))");
    }
}