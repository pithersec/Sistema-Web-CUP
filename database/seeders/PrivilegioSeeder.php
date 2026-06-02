<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivilegioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('privilegio')->insert([
            ['id' => 1, 'nombre' => 'sistema.total'],
            ['id' => 2, 'nombre' => 'usuarios.ver'],
            ['id' => 3, 'nombre' => 'usuarios.crear'],
            ['id' => 4, 'nombre' => 'usuarios.editar'],
            ['id' => 5, 'nombre' => 'usuarios.eliminar'],
            ['id' => 6, 'nombre' => 'perfiles.gestionar'],
            ['id' => 7, 'nombre' => 'privilegios.gestionar'],
            ['id' => 8, 'nombre' => 'postulantes.ver'],
            ['id' => 9, 'nombre' => 'postulantes.aprobar'],
            ['id' => 10, 'nombre' => 'postulantes.rechazar'],
            ['id' => 11, 'nombre' => 'postulantes.validar'],
            ['id' => 12, 'nombre' => 'docentes.ver'],
            ['id' => 13, 'nombre' => 'docentes.crear'],
            ['id' => 14, 'nombre' => 'docentes.editar'],
            ['id' => 15, 'nombre' => 'docentes.desactivar'],
            ['id' => 16, 'nombre' => 'carreras.ver'],
            ['id' => 17, 'nombre' => 'cupos.editar'],
            ['id' => 18, 'nombre' => 'grupos.ver'],
            ['id' => 19, 'nombre' => 'grupos.crear'],
            ['id' => 20, 'nombre' => 'materias.ver'],
            ['id' => 21, 'nombre' => 'materias.gestionar'],
            ['id' => 22, 'nombre' => 'gestiones.ver'],
            ['id' => 23, 'nombre' => 'gestiones.gestionar'],
            ['id' => 24, 'nombre' => 'notas.ver'],
            ['id' => 25, 'nombre' => 'notas.registrar'],
            ['id' => 26, 'nombre' => 'notas.editar'],
            ['id' => 27, 'nombre' => 'bitacora.ver'],
            ['id' => 28, 'nombre' => 'reportes.ver'],
        ]);
    }
}
