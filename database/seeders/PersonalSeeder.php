<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PersonalSeeder extends Seeder
{
    public function run(): void
    {
        $docentes = [
            ['3201845', 'Carlos',    'García Romero',    'm', '71234501', 'carlos.garcia@ficct.uagrm.edu.bo',    '1985-03-15', 'Av. Cañoto #101'],
            ['4502367', 'Luis',      'Rodríguez Torres', 'm', '71234502', 'luis.rodriguez@ficct.uagrm.edu.bo',   '1980-07-22', 'Calle Junín #202'],
            ['5103489', 'Jorge',     'López Flores',     'm', '71234503', 'jorge.lopez@ficct.uagrm.edu.bo',      '1978-11-10', 'Av. Roca #303'],
            ['6204512', 'Miguel',    'Martínez Suárez',  'm', '71234504', 'miguel.martinez@ficct.uagrm.edu.bo',  '1982-05-08', 'Calle Bolívar #404'],
            ['7305634', 'Sandra',    'González Vargas',  'f', '71234505', 'sandra.gonzalez@ficct.uagrm.edu.bo',  '1987-09-14', 'Av. Busch #505'],
            ['8406756', 'Claudia',   'Pérez Molina',     'f', '71234506', 'claudia.perez@ficct.uagrm.edu.bo',    '1984-02-28', 'Urb. Equipetrol #606'],
            ['9507878', 'Diego',     'Sánchez Morales',  'm', '71234507', 'diego.sanchez@ficct.uagrm.edu.bo',    '1979-06-17', 'Calle Florida #707'],
            ['1608901', 'Carmen',    'Romero Herrera',   'f', '71234508', 'carmen.romero@ficct.uagrm.edu.bo',    '1986-12-03', 'Av. Alemana #808'],
            ['2709023', 'Rosa',      'Torres Medina',    'f', '71234509', 'rosa.torres@ficct.uagrm.edu.bo',      '1983-04-25', 'Urb. Norte #909'],
            ['3810145', 'Pablo',     'Flores Aguilar',   'm', '71234510', 'pablo.flores@ficct.uagrm.edu.bo',     '1977-08-19', 'Calle Beni #1010'],
            ['4911267', 'Ricardo',   'Suárez Guzmán',    'm', '71234511', 'ricardo.suarez@ficct.uagrm.edu.bo',   '1981-01-30', 'Av. Banzer #1111'],
            ['5012389', 'Gonzalo',   'Vargas Castillo',  'm', '71234512', 'gonzalo.vargas@ficct.uagrm.edu.bo',   '1976-10-07', 'Urb. Las Palmas #1212'],
            ['6113412', 'Daniel',    'Molina Reyes',     'm', '71234513', 'daniel.molina@ficct.uagrm.edu.bo',    '1988-03-21', 'Calle 24 Sep #1313'],
            ['7214534', 'Andrés',    'Morales Mendoza',  'm', '71234514', 'andres.morales@ficct.uagrm.edu.bo',   '1985-07-14', 'Av. Cristo Rey #1414'],
            ['8315656', 'Sergio',    'Herrera Ramos',    'm', '71234515', 'sergio.herrera@ficct.uagrm.edu.bo',   '1980-11-26', 'Urb. Los Jardines #1515'],
            ['9416778', 'Javier',    'Medina Quispe',    'm', '71234516', 'javier.medina@ficct.uagrm.edu.bo',    '1978-05-09', 'Calle Cochabamba #1616'],
            ['1517801', 'Rodrigo',   'Castillo Chávez',  'm', '71234517', 'rodrigo.castillo@ficct.uagrm.edu.bo', '1982-09-18', 'Av. Cañoto #1717'],
            ['2618923', 'Mauricio',  'Reyes Salinas',    'm', '71234518', 'mauricio.reyes@ficct.uagrm.edu.bo',   '1986-02-11', 'Calle Junín #1818'],
            ['3719045', 'Patricia',  'Aguilar Navia',    'f', '71234519', 'patricia.aguilar@ficct.uagrm.edu.bo', '1984-06-23', 'Urb. Equipetrol #1919'],
            ['4820167', 'Silvia',    'Guzmán Pedraza',   'f', '71234520', 'silvia.guzman@ficct.uagrm.edu.bo',    '1979-10-05', 'Av. Busch #2020'],
            ['5921289', 'Ariel',     'Vargas Rivero',    'm', '71234521', 'ariel.vargas@ficct.uagrm.edu.bo',     '1977-04-16', 'Calle Florida #2121'],
            ['6022312', 'Esteban',   'Soliz Antelo',     'm', '71234522', 'esteban.soliz@ficct.uagrm.edu.bo',    '1983-08-29', 'Av. Alemana #2222'],
            ['7123434', 'Nicolás',   'Cruz Justiniano',  'm', '71234523', 'nicolas.cruz@ficct.uagrm.edu.bo',     '1981-12-12', 'Urb. Norte #2323'],
            ['8224556', 'Cristian',  'Montaño Balcázar', 'm', '71234524', 'cristian.montano@ficct.uagrm.edu.bo', '1987-03-04', 'Calle Beni #2424'],
            ['9325678', 'Gabriela',  'Veizaga Cuellar',  'f', '71234525', 'gabriela.veizaga@ficct.uagrm.edu.bo', '1985-07-27', 'Av. Banzer #2525'],
            ['1426701', 'Adriana',   'Salinas Rojas',    'f', '71234526', 'adriana.salinas@ficct.uagrm.edu.bo',  '1980-11-19', 'Urb. Las Palmas #2626'],
            ['2527823', 'Fernando',  'Chávez Vega',      'm', '71234527', 'fernando.chavez@ficct.uagrm.edu.bo',  '1978-05-08', 'Calle 24 Sep #2727'],
            ['3628945', 'Elena',     'Ramos Jiménez',    'f', '71234528', 'elena.ramos@ficct.uagrm.edu.bo',      '1982-09-21', 'Av. Cristo Rey #2828'],
            ['4729067', 'Beatriz',   'Jiménez Cruz',     'f', '71234529', 'beatriz.jimenez@ficct.uagrm.edu.bo',  '1986-01-14', 'Urb. Los Jardines #2929'],
            ['5830189', 'Víctor',    'Pedraza Antelo',   'm', '71234530', 'victor.pedraza@ficct.uagrm.edu.bo',   '1984-06-07', 'Calle Cochabamba #3030'],
        ];

        $requisitosConfig = [
            0  => ['matematicas'],
            1  => ['fisica'],
            2  => ['ingles'],
            3  => ['computacion'],
            4  => ['matematicas'],
            5  => ['fisica'],
            6  => ['ingles'],
            7  => ['computacion'],
            8  => ['matematicas'],
            9  => ['fisica'],
            10 => ['ingles'],
            11 => ['computacion'],
            12 => ['matematicas'],
            13 => ['fisica'],
            14 => ['ingles'],
            15 => ['computacion'],
            16 => ['matematicas'],
            17 => ['fisica'],
            18 => ['ingles'],
            19 => ['computacion'],
            20 => ['matematicas'],
            21 => ['fisica'],
            22 => ['matematicas', 'fisica'],
            23 => ['ingles', 'computacion'],
            24 => ['fisica', 'computacion'],
            25 => ['matematicas', 'ingles'],
            26 => ['matematicas', 'fisica', 'ingles'],
            27 => ['fisica', 'ingles', 'computacion'],
            28 => ['matematicas', 'computacion', 'ingles'],
            29 => ['matematicas', 'fisica', 'computacion'],
        ];

        $grados = ['licenciatura', 'ingenieria', 'maestria', 'tecnico_superior'];

        $datosPersonalesRows = [];
        $personalRows        = [];
        $requisitosRows      = [];
        $usuarioRows         = [];

        foreach ($docentes as $i => $d) {
            [$ci, $nombre, $apellido, $genero, $telefono, $correo, $fechaNac, $direccion] = $d;
            $registro = (string)(1001 + $i);

            $primerApellido = strtolower(explode(' ', $apellido)[0]);
            $primerApellido = str_replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'], $primerApellido);
            $inicial        = strtolower(substr($nombre, 0, 1));
            $userName       = $inicial . $primerApellido . str_pad($i + 1, 2, '0', STR_PAD_LEFT);

            $datosPersonalesRows[] = [
                'ci'        => $ci,
                'nombre'    => $nombre,
                'apellido'  => $apellido,
                'genero'    => $genero,
                'telefono'  => $telefono,
                'correo'    => $correo,
                'fecha_nac' => $fechaNac,
                'direccion' => $direccion,
            ];

            $personalRows[] = [
                'registro' => $registro,
                'ci'       => $ci,
                'estado'   => true,
            ];

            foreach ($requisitosConfig[$i] as $area) {
                $grado = $grados[$i % count($grados)];
                $requisitosRows[] = [
                    'registro_personal' => $registro,
                    'area'              => $area,
                    'nivel_grado'       => $grado,
                    'nivel_exp'         => 5 + ($i % 15),
                    'maestria'          => $grado === 'maestria',
                    'doctorado'         => false,
                    'diplomado'         => $i % 3 === 0,
                ];
            }

            $usuarioRows[] = [
                'user_name'         => $userName,
                'clave'             => Hash::make($ci),
                'email'             => $correo,
                'id_perfil'         => 3,
                'registro_personal' => $registro,
            ];
        }

        DB::table('datos_personales')->insert($datosPersonalesRows);
        DB::table('personal')->insert($personalRows);
        DB::table('requisitos_personal')->insert($requisitosRows);
        DB::table('usuario')->insert($usuarioRows);
    }
}