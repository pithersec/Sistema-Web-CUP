<?php

namespace App\Http\Controllers;

use App\Models\Postulante;
use App\Models\Grupo;
use App\Models\Examen;
use App\Models\Carrera;
use App\Models\Gestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $totalPostulantes = Postulante::count();
            $totalGrupos = Grupo::count();

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

            $kpis = [
                'total_inscritos'    => $totalPostulantes,
                'total_aprobados'    => $totalAprobados,
                'total_reprobados'   => $totalReprobados,
                'grupos_habilitados' => $totalGrupos
            ];

            $gestionActual = Gestion::orderBy('codigo', 'desc')->first();
        $gestionCodigo = $gestionActual ? $gestionActual->codigo : null;

        if ($gestionCodigo) {
            $resumenCarreras = DB::table('carrera')
                ->leftJoin('carrera_gestion', function ($join) use ($gestionCodigo) {
                    $join->on('carrera.codigo', '=', 'carrera_gestion.codigo_carrera')
                        ->where('carrera_gestion.codigo_gestion', '=', $gestionCodigo);
                })
                ->leftJoin('postulante_carrera', function ($join) {
                    $join->on('carrera.codigo', '=', 'postulante_carrera.codigo_carrera')
                        ->where('postulante_carrera.opcion', '=', 1);
                })
                ->leftJoin('postulante', 'postulante_carrera.codigo_postulante', '=', 'postulante.codigo')
                ->select(
                    'carrera.codigo',
                    'carrera.nombre as carrera_nombre',
                    'carrera.modalidad',
                    DB::raw('COALESCE(carrera_gestion.cupos, 0) as total_cupos'),
                    DB::raw('COUNT(postulante.codigo) as total_postulantes'),
                    DB::raw("SUM(CASE WHEN postulante.estado = 'aprobado' THEN 1 ELSE 0 END) as total_aprobados")
                )
                ->groupBy('carrera.codigo', 'carrera.nombre', 'carrera.modalidad', 'carrera_gestion.cupos')
                ->get();
        } else {
            $resumenCarreras = collect([]);
        }

            return view('dashboard', compact('kpis', 'resumenCarreras'));

        } catch (\Exception $e) {
            Log::error('Error en Dashboard: ' . $e->getMessage());
            return view('dashboard', [
                'kpis' => [
                    'total_inscritos' => 0,
                    'total_aprobados' => 0,
                    'total_reprobados' => 0,
                    'grupos_habilitados' => 0,
                ],
                'resumenCarreras' => collect([]),
            ]);
        }
    }
}