<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PostulanteSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_BO');

        // Al inicio, carga los grupos por gestión
        $gruposPorGestion = [
            '1-2025' => DB::table('grupo')->where('codigo_gestion', '1-2025')->pluck('id')->toArray(),
            '2-2025' => DB::table('grupo')->where('codigo_gestion', '2-2025')->pluck('id')->toArray(),
            '1-2026' => DB::table('grupo')->where('codigo_gestion', '1-2026')->pluck('id')->toArray(),
        ];

        $gestiones = [
            ['codigo' => '1-2025', 'total' => 847, 'plazo_ini' => '2025-01-15', 'plazo_fin' => '2025-03-30'],
            ['codigo' => '2-2025', 'total' => 1134, 'plazo_ini' => '2025-07-10', 'plazo_fin' => '2025-09-30'],
            ['codigo' => '1-2026', 'total' => 923, 'plazo_ini' => '2026-01-10', 'plazo_fin' => '2026-03-30'],
        ];

        $carreras = [
            ['codigo' => '323', 'plan' => '0', 'modalidad' => 'presencial'],
            ['codigo' => '187', 'plan' => '4', 'modalidad' => 'presencial'],
            ['codigo' => '187', 'plan' => '5', 'modalidad' => 'presencial'],
            ['codigo' => '187', 'plan' => '6', 'modalidad' => 'presencial'],
            ['codigo' => '187', 'plan' => '3', 'modalidad' => 'virtual'],
            ['codigo' => '187', 'plan' => '4', 'modalidad' => 'virtual'],
        ];

        $estadosPorGestion = [
            '1-2025' => ['aprobado' => 45, 'reprobado' => 50, 'inscrito' => 3, 'preinscrito' => 2],
            '2-2025' => ['aprobado' => 40, 'reprobado' => 53, 'inscrito' => 4, 'preinscrito' => 3],
            '1-2026' => ['inscrito' => 60, 'preinscrito' => 40],
        ];

        $colegios = [1, 2, 3, 4, 5, 6, 7, 8];
        $ciCounter = 1000000;
        $postulantes = [];
        $postulantesCarrera = [];
        $datosPersonales = [];
        $pagos = [];
        $requisitos = [];
        $pagoId = 4; // empieza después de los 3 del PagoSeeder
        $requisitosId = 4;

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
                $ci = (string)($ciCounter++);
                $codigo = "POST-{$gestionCorta}-" . str_pad($num++, 4, '0', STR_PAD_LEFT);

                $datosPersonales[] = [
                    'ci'        => $ci,
                    'nombre'    => $faker->firstName(),
                    'apellido'  => $faker->lastName(),
                    'genero'    => $faker->randomElement(['m', 'f']),
                    'telefono'  => '7' . $faker->numerify('#######'),
                    'correo'    => $faker->unique()->safeEmail(),
                    'fecha_nac' => $faker->dateTimeBetween('-25 years', '-16 years')->format('Y-m-d'),
                    'direccion' => $faker->address(),
                ];

                $pagos[] = [
                    'id'                  => $pagoId,
                    'monto'               => $faker->randomElement([300.00, 350.00]),
                    'fecha'               => $faker->dateTimeBetween($gestion['plazo_ini'], $gestion['plazo_fin'])->format('Y-m-d H:i:s'),
                    'concepto'            => "Inscripción CUP {$gestion['codigo']}",
                    'estado'              => 'completado',
                    'referencia_pasarela' => 'PAGO-' . strtoupper($faker->bothify('??##??##')),
                ];

                $requisitos[] = [
                    'id'              => $requisitosId,
                    'titulo_original' => true,
                    'titulo_copia'    => true,
                    'fotocopia_carnet'=> true,
                    'formulario'      => true,
                    'comprobante'     => true,
                    'libreta'         => $faker->boolean(80),
                ];

                $postulantes[] = [
                    'codigo'                   => $codigo,
                    'ci'                       => $ci,
                    'procedencia'              => $faker->city(),
                    'telefono_2'               => $faker->boolean(40) ? '6' . $faker->numerify('#######') : null,
                    'plazo'                    => $faker->dateTimeBetween($gestion['plazo_ini'], $gestion['plazo_fin'])->format('Y-m-d'),
                    'estado'                   => $estado,
                    'gestion_egreso'           => (string)($faker->numberBetween(2020, 2025)),
                    'id_requisitos_postulante' => $requisitosId,
                    'id_colegio'               => $faker->randomElement($colegios),
                    'id_pago'                  => $pagoId,
                    'id_grupo'                 => $faker->randomElement($gruposPorGestion[$gestion['codigo']]),
                ];

                // Elegir 2 carreras distintas
                $carrerasShuffled = $carreras;
                shuffle($carrerasShuffled);
                $postulantesCarrera[] = [
                    'codigo_postulante' => $codigo,
                    'codigo_carrera'    => $carrerasShuffled[0]['codigo'],
                    'plan_carrera'      => $carrerasShuffled[0]['plan'],
                    'modalidad_carrera' => $carrerasShuffled[0]['modalidad'],
                    'opcion'            => 1,
                ];
                $postulantesCarrera[] = [
                    'codigo_postulante' => $codigo,
                    'codigo_carrera'    => $carrerasShuffled[1]['codigo'],
                    'plan_carrera'      => $carrerasShuffled[1]['plan'],
                    'modalidad_carrera' => $carrerasShuffled[1]['modalidad'],
                    'opcion'            => 2,
                ];

                $pagoId++;
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
    }
}