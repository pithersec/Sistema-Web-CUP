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

        // Al inicio, carga los grupos por gestión
        $estadosPorGestion = [
            '1-2025' => ['aprobado' => 45, 'reprobado' => 50, 'inscrito' => 3, 'preinscrito' => 2],
            '2-2025' => ['aprobado' => 40, 'reprobado' => 53, 'inscrito' => 4, 'preinscrito' => 3],
            '1-2026' => ['aprobado' => 42, 'reprobado' => 51, 'inscrito' => 4, 'preinscrito' => 3],
            '2-2026' => ['inscrito' => 55, 'preinscrito' => 45],
        ];

        $gestiones = [
            ['codigo' => '1-2025', 'total' => 847,  'plazo_ini' => '2025-01-02', 'plazo_fin' => '2025-02-16'],
            ['codigo' => '2-2025', 'total' => 1134, 'plazo_ini' => '2025-06-02', 'plazo_fin' => '2025-07-20'],
            ['codigo' => '1-2026', 'total' => 923,  'plazo_ini' => '2026-01-05', 'plazo_fin' => '2026-02-15'],
            ['codigo' => '2-2026', 'total' => 821,  'plazo_ini' => '2026-06-03', 'plazo_fin' => '2026-07-20'],
        ];

        $gruposPorGestion = [
            '1-2025' => DB::table('grupo')->where('codigo_gestion', '1-2025')->pluck('id')->toArray(),
            '2-2025' => DB::table('grupo')->where('codigo_gestion', '2-2025')->pluck('id')->toArray(),
            '1-2026' => DB::table('grupo')->where('codigo_gestion', '1-2026')->pluck('id')->toArray(),
            '2-2026' => DB::table('grupo')->where('codigo_gestion', '2-2026')->pluck('id')->toArray(),
        ];

        $carreras = [
            ['codigo' => '187', 'plan' => '4', 'modalidad' => 'presencial', 'peso' => 28],
            ['codigo' => '187', 'plan' => '6', 'modalidad' => 'presencial', 'peso' => 21],
            ['codigo' => '187', 'plan' => '5', 'modalidad' => 'presencial', 'peso' => 14],
            ['codigo' => '187', 'plan' => '4', 'modalidad' => 'virtual',    'peso' => 14],
            ['codigo' => '187', 'plan' => '3', 'modalidad' => 'virtual',    'peso' => 14],
            ['codigo' => '323', 'plan' => '0', 'modalidad' => 'presencial', 'peso' => 9],
        ];

        // Función para elegir con peso
        $elegirCarrera = function() use ($carreras, $faker) {
            $total = array_sum(array_column($carreras, 'peso'));
            $rand = $faker->numberBetween(1, $total);
            $acumulado = 0;
            foreach ($carreras as $c) {
                $acumulado += $c['peso'];
                if ($rand <= $acumulado) return $c;
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

        $colegios = [1, 2, 3, 4, 5, 6, 7, 8];
        $usedCis = [];
        $postulantes = [];
        $postulantesCarrera = [];
        $datosPersonales = [];
        $pagos = [];
        $requisitos = [];

        $pagoId = 1;
        $requisitosId = 1;

        foreach ($gestiones as $gestion) {
            $estados = $estadosPorGestion[$gestion['codigo']];
            $lista = [];
            foreach ($estados as $estado => $porcentaje) {
                $cantidad = (int) round($gestion['total'] * $porcentaje / 100);
                for ($i = 0; $i < $cantidad; $i++) {
                    $lista[] = $estado;
                }
            }
            // Rellenar si hay diferencia por redondeo
            while (count($lista) < $gestion['total']) {
                $lista[] = 'preinscrito';
            }
            shuffle($lista);

            $gestionCorta = str_replace('-', '', $gestion['codigo']);
            $num = 1;

            foreach ($lista as $estado) {
                do {
                    $ci = (string)$faker->numberBetween(1000000, 9999999);
                } while (in_array($ci, $usedCis));
                $usedCis[] = $ci;
                $codigo = $gestionCorta . str_pad($num++, 4, '0', STR_PAD_LEFT);

                $genero = $faker->randomElement(['m', 'f']);

                $nombreLower = strtolower($faker->randomElement($nombresPorGenero[$genero]));
                $apellidoLower = strtolower($faker->randomElement($apellidos));
                $numero = $faker->numberBetween(1, 999);
                $dominio = $faker->randomElement(['gmail.com', 'gmail.com', 'gmail.com', 'hotmail.com', 'outlook.com']);
                $formato = $faker->randomElement([
                    $nombreLower . '.' . $apellidoLower . $numero . $ci,
                    substr($nombreLower, 0, 1) . $apellidoLower . $numero . $ci,
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

                $tieneRequisitosCompletos = in_array($estado, ['inscrito', 'aprobado', 'reprobado']);

                $requisitos[] = [
                    'id'              => $requisitosId,
                    'titulo_original' => $tieneRequisitosCompletos ? true : $faker->boolean(60),
                    'titulo_copia'    => $tieneRequisitosCompletos ? true : $faker->boolean(60),
                    'fotocopia_carnet'=> $tieneRequisitosCompletos ? true : $faker->boolean(60),
                    'formulario'      => $tieneRequisitosCompletos ? true : $faker->boolean(60),
                    'comprobante'     => $tieneRequisitosCompletos ? true : $faker->boolean(60),
                    'libreta'         => $faker->boolean($tieneRequisitosCompletos ? 80 : 40),
                ];

                $grupoAsignado = $faker->randomElement($gruposPorGestion[$gestion['codigo']]);
                $turnoGrupo = DB::table('grupo')
                    ->where('id', $grupoAsignado)
                    ->where('codigo_gestion', $gestion['codigo'])
                    ->value('nombre_turno');

                $postulantes[] = [
                    'codigo'                   => $codigo,
                    'ci'                       => $ci,
                    'procedencia'              => $faker->randomElement($departamentos),
                    'telefono_2'               => $faker->boolean(40) ? '6' . $faker->numerify('#######') : null,
                    'plazo'                    => $faker->dateTimeBetween($gestion['plazo_ini'], $gestion['plazo_fin'])->format('Y-m-d'),
                    'estado'                   => $estado,
                    'gestion_egreso'           => (string)($faker->numberBetween(2021, 2025)),
                    'id_requisitos_postulante' => $requisitosId,
                    'id_colegio'               => $faker->randomElement($colegios),
                    'id_pago'                  => $tienePago ? $pagoId : null,
                    'id_grupo'                 => $grupoAsignado,
                    'gestion_grupo'            => $gestion['codigo'],
                    'nombre_turno'             => $turnoGrupo,
                    'estado_formulario'        => 'activo',
                ];

                // Elegir 2 carreras distintas
                $carrera1 = $elegirCarrera();
                do { $carrera2 = $elegirCarrera(); } 
                while ($carrera2 == $carrera1); // que sean distintas
                $postulantesCarrera[] = [
                    'codigo_postulante' => $codigo,
                    'codigo_carrera'    => $carrera1['codigo'],
                    'plan_carrera'      => $carrera1['plan'],
                    'modalidad_carrera' => $carrera1['modalidad'],
                    'opcion'            => 1,
                ];
                $postulantesCarrera[] = [
                    'codigo_postulante' => $codigo,
                    'codigo_carrera'    => $carrera2['codigo'],
                    'plan_carrera'      => $carrera2['plan'],
                    'modalidad_carrera' => $carrera2['modalidad'],
                    'opcion'            => 2,
                ];

                if ($tienePago) $pagoId++;
                $requisitosId++;
            }
        }

        // Insertar en chunks para no explotar la memoria
        foreach (array_chunk($datosPersonales, 500) as $chunk) {
            DB::table('datos_personales')->insert($chunk);
        }
        foreach (array_chunk($requisitos, 500) as $chunk) {
            DB::table('requisitos_postulante')->insert($chunk);
        }
        foreach (array_chunk($pagos, 500) as $chunk) {
            DB::table('pago')->insert($chunk);
        }
        foreach (array_chunk($postulantes, 500) as $chunk) {
            DB::table('postulante')->insert($chunk);
        }
        foreach (array_chunk($postulantesCarrera, 500) as $chunk) {
            DB::table('postulante_carrera')->insert($chunk);
        }

        // Al final de run(), después de todos los inserts
        DB::statement("SELECT setval('requisitos_postulante_id_seq', (SELECT MAX(id) FROM requisitos_postulante))");
        DB::statement("SELECT setval('pago_id_seq', (SELECT MAX(id) FROM pago))");
    }
}