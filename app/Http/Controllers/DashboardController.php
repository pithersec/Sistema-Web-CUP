<?php

namespace App\Http\Controllers;

use App\Models\Postulante;
use App\Models\Grupo;
use App\Models\Examen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * CU-09 y CU-19: Consultar indicadores estadísticos y visualizar panel (ACTUALIZADO)
     * Aplica la regla: 3 parciales (30%, 30%, 40%) y nota mínima de 60 en CADA UNO para aprobar.
     */
    public function obtenerIndicadores()
    {
        $totalPostulantes = Postulante::count();
        $totalGrupos = Grupo::count();

        // [MANTIENE TU LOGICA INTACTA] Evaluación de rendimiento individual
        $postulantesConExamenes = Examen::select('codigo_postulante')
            ->selectRaw("COUNT(CASE WHEN nro_examen = 1 THEN 1 END) as tiene_p1")
            ->selectRaw("MIN(CASE WHEN nro_examen = 1 THEN nota END) as nota_p1")
            ->selectRaw("COUNT(CASE WHEN nro_examen = 2 THEN 1 END) as tiene_p2")
            ->selectRaw("MIN(CASE WHEN nro_examen = 2 THEN nota END) as nota_p2")
            ->selectRaw("COUNT(CASE WHEN nro_examen = 3 THEN 1 END) as tiene_pf")
            ->selectRaw("MIN(CASE WHEN nro_examen = 3 THEN nota END) as nota_pf")
            ->groupBy('codigo_postulante')
            ->get();

        $totalAprobados = 0;
        $totalReprobados = 0;

        foreach ($postulantesConExamenes as $registro) {
            if ($registro->tiene_p1 > 0 && $registro->nota_p1 >= 60 &&
                $registro->tiene_p2 > 0 && $registro->nota_p2 >= 60 &&
                $registro->tiene_pf > 0 && $registro->nota_pf >= 60) {
                
                $totalAprobados++;
            } else {
                $totalReprobados++;
            }
        }

        $postulantesSinNingunaNota = $totalPostulantes - $postulantesConExamenes->count();
        $totalReprobados += $postulantesSinNingunaNota;

        // ----------------------------------------------------------------------
        // NUEVO ADICIONAL PARA CU-19: Estructura de KPIs para la vista Blade
        // ----------------------------------------------------------------------
        $kpis = [
            'total_inscritos'    => $totalPostulantes,
            'total_aprobados'    => $totalAprobados,
            'total_reprobados'   => $totalReprobados,
            'grupos_habilitados' => $totalGrupos
        ];

        // NUEVO ADICIONAL PARA CU-19: Consulta para la tabla inferior de "Resumen por Carrera"
        // (Ajusta los nombres de las tablas/columnas si varían en tu migración)
        $resumenCarreras = DB::table('carrera')
            ->leftJoin('carrera_gestion', 'carrera.codigo', '=', 'carrera_gestion.codigo_carrera')
            ->leftJoin('postulante', 'carrera.codigo', '=', 'postulante.codigo_carrera1')
            ->select(
                'carrera.nombre as carrera_nombre',
                DB::raw('COALESCE(carrera_gestion.cupos, 0) as total_cupos'),
                DB::raw('COUNT(postulante.codigo) as total_postulantes'),
                // Cuenta cuántos aprobados hay en esta carrera basándose en la columna estado
                DB::raw('SUM(CASE WHEN postulante.estado = "Aprobado" THEN 1 ELSE 0 END) as total_aprobados')
            )
            ->groupBy('carrera.codigo', 'carrera.nombre', 'carrera_gestion.cupos')
            ->get();

        // RETORNO MODIFICADO: Ahora renderiza tu vista e inyecta los datos calculados
        return view('dashboard', compact('kpis', 'resumenCarreras'));
    }
}
