<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Gestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Gestiones ordenadas: año desc, semestre desc
            $gestiones = Gestion::orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")->get();

            // Gestión seleccionada
            $gestionCodigo = $request->get('gestion',
                $gestiones->first()?->codigo
            );
            $gestionActual = Gestion::find($gestionCodigo);

            // IDs de grupos de esa gestión
            $idsGrupos = Grupo::where('codigo_gestion', $gestionCodigo)->pluck('id');

            // Postulantes de esa gestión (via grupo)
            $postulantes = DB::table('postulante')
                ->whereIn('id_grupo', $idsGrupos)
                ->where('gestion_grupo', $gestionCodigo)    
                ->select('codigo', 'estado')
                ->get();

            $codigosPostulantes = $postulantes->pluck('codigo');
            $totalPostulantes   = $postulantes->count();
            $totalAprobados     = $postulantes->where('estado', 'aprobado')->count();
            $totalReprobados    = $postulantes->where('estado', 'reprobado')->count();
            $totalGrupos        = $idsGrupos->count();
            
            $cuposTotales = DB::table('carrera_gestion')
                ->where('codigo_gestion', $gestionCodigo)
                ->sum('cupos');

            $tasaAprobacion = $totalPostulantes > 0
                ? round(($totalAprobados / $totalPostulantes) * 100)
                : 0;

            $kpis = [
                'total_inscritos'    => $totalPostulantes,
                'total_aprobados'    => $totalAprobados,
                'total_reprobados'   => $totalReprobados,
                'grupos_habilitados' => $totalGrupos,
                'cupos_totales'      => $cuposTotales,
                'tasa_aprobacion'    => $tasaAprobacion,
            ];

            // Resumen por carrera — solo carreras activas en la gestión
            $resumenCarreras = DB::table('carrera')
                ->join('carrera_gestion', function ($join) use ($gestionCodigo) {
                    $join->on('carrera.codigo', '=', 'carrera_gestion.codigo_carrera')
                        ->on('carrera.plan', '=', 'carrera_gestion.plan_carrera')
                        ->on('carrera.modalidad', '=', 'carrera_gestion.modalidad_carrera')
                        ->where('carrera_gestion.codigo_gestion', '=', $gestionCodigo);
                })
                ->leftJoin('postulante_carrera', function ($join) {
                    $join->on('carrera.codigo', '=', 'postulante_carrera.codigo_carrera')
                        ->on('carrera.plan', '=', 'postulante_carrera.plan_carrera')        // ← agregar
                        ->on('carrera.modalidad', '=', 'postulante_carrera.modalidad_carrera') // ← agregar
                        ->where('postulante_carrera.opcion', '=', 1);
                })
                ->leftJoin('postulante', function ($join) use ($codigosPostulantes) {
                    $join->on('postulante_carrera.codigo_postulante', '=', 'postulante.codigo')
                        ->whereIn('postulante.codigo', $codigosPostulantes);
                })
                ->select(
                    'carrera.codigo',
                    'carrera.plan',
                    'carrera.nombre as carrera_nombre',
                    'carrera.modalidad',
                    'carrera_gestion.cupos as total_cupos',
                    DB::raw('COUNT(DISTINCT postulante.codigo) as total_postulantes'),
                    DB::raw("SUM(CASE WHEN postulante.estado = 'aprobado' THEN 1 ELSE 0 END) as total_aprobados"),
                    DB::raw("SUM(CASE WHEN postulante.estado = 'reprobado' THEN 1 ELSE 0 END) as total_reprobados")
                )
                ->groupBy('carrera.codigo', 'carrera.plan', 'carrera.nombre', 'carrera.modalidad', 'carrera_gestion.cupos')
                ->orderByRaw("CASE WHEN carrera.modalidad = 'presencial' THEN 0 ELSE 1 END ASC")
                ->orderBy('carrera_gestion.cupos', 'desc')
                ->orderBy('carrera.nombre')
                ->get();

            return view('dashboard', compact('kpis', 'resumenCarreras', 'gestionActual', 'gestiones', 'gestionCodigo'));

        } catch (\Exception $e) {
            Log::error('Error en Dashboard: ' . $e->getMessage());
            return view('dashboard', [
                'kpis' => ['total_inscritos' => 0, 'total_aprobados' => 0, 'total_reprobados' => 0, 'grupos_habilitados' => 0],
                'resumenCarreras' => collect([]),
                'gestionActual'   => null,
                'gestiones'       => collect([]),
                'gestionCodigo'   => null,
            ]);
        }
    }
}