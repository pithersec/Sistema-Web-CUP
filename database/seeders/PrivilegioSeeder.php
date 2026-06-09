<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivilegioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('privilegio')->insert([
            ['id' => 1,  'nombre' => 'sistema.total'],
            ['id' => 2,  'nombre' => 'usuarios.ver'],
            ['id' => 3,  'nombre' => 'usuarios.editar'],
            ['id' => 4,  'nombre' => 'usuarios.cargar'],
            ['id' => 5,  'nombre' => 'perfiles.gestionar'],
            ['id' => 6,  'nombre' => 'postulantes.ver'],
            ['id' => 7,  'nombre' => 'postulantes.editar'],
            ['id' => 8,  'nombre' => 'postulantes.validar'],    
            ['id' => 9,  'nombre' => 'personal.ver'],
            ['id' => 10, 'nombre' => 'personal.crear'],
            ['id' => 11, 'nombre' => 'personal.editar'],
            ['id' => 12, 'nombre' => 'personal.desactivar'],
            ['id' => 13, 'nombre' => 'carreras.ver'],
            ['id' => 14, 'nombre' => 'cupos.editar'],
            ['id' => 15, 'nombre' => 'grupos.ver'],
            ['id' => 16, 'nombre' => 'grupos.asignar'],
            ['id' => 17, 'nombre' => 'materias.gestionar'],
            ['id' => 18, 'nombre' => 'gestiones.ver'],
            ['id' => 19, 'nombre' => 'gestiones.gestionar'],
            ['id' => 20, 'nombre' => 'notas.ver'],
            ['id' => 21, 'nombre' => 'notas.registrar'],
            ['id' => 22, 'nombre' => 'notas.editar'],
            ['id' => 23, 'nombre' => 'bitacora.ver'],
            ['id' => 24, 'nombre' => 'reportes.ver'],
            ['id' => 25, 'nombre' => 'rendimiento.ver'],
            ['id' => 26, 'nombre' => 'reclamos.gestionar'],
            ['id' => 27, 'nombre' => 'asistencia.registrar'],
            ['id' => 28, 'nombre' => 'configuracion.gestionar'],
        ]);
    }
}
