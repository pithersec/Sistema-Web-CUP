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
     * CU-09 y CU-19: Consultar indicadores estadísticos y visualizar panel.
     * Mantiene intacta la regla del CUP: 3 parciales y nota mínima de 60 en CADA UNO para aprobar.
     */
    public function index() // 👈 Cambiado a index para seguir el estándar de Laravel
    {
        $totalPostulantes = Postulante::count();
        $totalGrupos = Grupo::count();

        // Tu lógica exacta de evaluación de rendimiento individual
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

        // Estructura de KPIs para la hermosa vista que armamos
        $kpis = [
            'total_inscritos'    => $totalPostulantes,
            'total_aprobados'    => $totalAprobados,
            'total_reprobados'   => $totalReprobados,
            'grupos_habilitados' => $totalGrupos
        ];

        // Tu consulta real adaptada a tu mapa de Base de Datos del CUP
        $resumenCarreras = DB::table('carrera')
            ->leftJoin('carrera_gestion', 'carrera.codigo', '=', 'carrera_gestion.codigo_carrera')
            ->leftJoin('postulante', 'carrera.codigo', '=', 'postulante.codigo_carrera1')
            ->select(
                'carrera.nombre as carrera_nombre',
                DB::raw('COALESCE(carrera_gestion.cupos, 0) as total_cupos'),
                DB::raw('COUNT(postulante.codigo) as total_postulantes'),
                DB::raw('SUM(CASE WHEN postulante.estado = "Aprobado" THEN 1 ELSE 0 END) as total_aprobados')
            )
            ->groupBy('carrera.codigo', 'carrera.nombre', 'carrera_gestion.cupos')
            ->get();

        return view('dashboard', compact('kpis', 'resumenCarreras'));
    }
}