<?php

namespace App\Http\Controllers;

use App\Models\Gestion;
use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReporteExport;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $gestiones = Gestion::orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")->get();
        $carrera     = $request->input('carrera', '');
        $carreraParts = $carrera ? explode('|', $carrera) : [];
        $codigoCarrera   = $carreraParts[0] ?? null;
        $planCarrera     = $carreraParts[1] ?? null;
        $modalidadCarrera = $carreraParts[2] ?? null;
        $carreras = Carrera::orderBy('modalidad')->orderBy('nombre')->get();
        $materias  = DB::table('materia')->orderBy('nombre')->get();
        $turnos    = DB::table('turno')->orderByRaw("CASE WHEN nombre='mañana' THEN 0 WHEN nombre='tarde' THEN 1 ELSE 2 END")->get();

        $tipoActual  = $request->input('tipo_reporte', '');
        $gestion     = $request->input('gestion', '');
        $carrera     = $request->input('carrera', '');
        $turno       = $request->input('turno', '');
        $materia     = $request->input('materia', '');
        $fechaInicio = $request->input('fecha_inicio', '');
        $fechaFin    = $request->input('fecha_fin', '');

        $titulo  = '';
        $columnas = [];
        $filas   = collect();
        $totalFilas = 0;

        if ($tipoActual) {
            [$titulo, $columnas, $todasFilas] = $this->obtenerDatos(
                $tipoActual, $gestion, $codigoCarrera, $planCarrera, $modalidadCarrera, $turno, $materia, $fechaInicio, $fechaFin
            );
            $totalFilas = $todasFilas->count();
            $page  = $request->input('page', 1);
            $filas = new \Illuminate\Pagination\LengthAwarePaginator(
                $todasFilas->forPage($page, 15),
                $totalFilas,
                15,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        return view('reportes.index', compact(
            'gestiones', 'carreras', 'materias', 'turnos',
            'tipoActual', 'gestion', 'carrera', 'turno', 'materia',
            'fechaInicio', 'fechaFin', 'titulo', 'columnas', 'filas', 'totalFilas'
        ));
    }

    public function exportar(Request $request)
    {
        $tipo     = $request->input('tipo_reporte');
        $gestion  = $request->input('gestion');
        $carrera  = $request->input('carrera', '');
        $carreraParts     = $carrera ? explode('|', $carrera) : [];
        $codigoCarrera    = $carreraParts[0] ?? null;
        $planCarrera      = $carreraParts[1] ?? null;
        $modalidadCarrera = $carreraParts[2] ?? null;
        $turno    = $request->input('turno');
        $materia  = $request->input('materia');
        $fechaIni = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $formato  = $request->input('formato', 'pdf');

        [$titulo, $columnas, $filas] = $this->obtenerDatos(
            $tipo, $gestion, $codigoCarrera, $planCarrera, $modalidadCarrera, $turno, $materia, $fechaIni, $fechaFin
        );

        if ($formato === 'excel') {
            return Excel::download(
                new ReporteExport($titulo, $columnas, $filas),
                $this->nombreArchivo($tipo, 'xlsx')
            );
        }

        if ($formato === 'pdf') {
            $limite  = 1000;
            $total   = $filas->count();
            $cortado = $total > $limite;
            $filas   = $filas->take($limite);

            $pdf = Pdf::loadView('reportes.pdf', compact('titulo', 'columnas', 'filas', 'total', 'cortado'))
                ->setPaper('a4', 'landscape')
                ->setOption('dpi', 96)
                ->setOption('default-font-size', 9);

            return $pdf->download($this->nombreArchivo($tipo, 'pdf'));
        }

        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $pdf = Pdf::loadView('reportes.pdf', compact('titulo', 'columnas', 'filas'))
            ->setPaper('a4', 'landscape');

        return $pdf->download($this->nombreArchivo($tipo, 'pdf'));
    }

    // -------------------------------------------------------------------
    private function obtenerDatos($tipo, $gestion, $carrera, $plan, $modalidad, $turno, $materia, $fechaIni, $fechaFin): array
    {
        return match($tipo) {
            'postulantes'         => $this->listaPostulantes($gestion, $carrera, $plan, $modalidad),
            'aprobados'           => $this->listaAprobados($gestion, $carrera, $plan, $modalidad),
            'reprobados'          => $this->listaReprobados($gestion, $carrera, $plan, $modalidad),
            'promedios_materia'   => $this->promediosPorMateria($gestion),
            'estadisticas_materia'=> $this->estadisticasPorMateria($gestion),
            'docentes_grupo'      => $this->docentesPorGrupo($gestion, $turno),
            'grupos_aprobados'    => $this->gruposConMasAprobados($gestion),
            'recaudacion'         => $this->recaudacion($fechaIni, $fechaFin),
            'promedios_generales' => $this->promediosGenerales($gestion, $carrera, $plan, $modalidad),
            'grupos_habilitados'  => $this->gruposHabilitados($gestion, $turno),
            'asistencia'          => $this->listaAsistencia($gestion, $materia, $turno),
            default               => ['Reporte desconocido', [], collect()],
        };
    }

    private function listaPostulantes($gestion, $carrera, $plan, $modalidad): array
    {
        $q = DB::table('postulante')
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->join('postulante_carrera', 'postulante.codigo', '=', 'postulante_carrera.codigo_postulante')
            ->join('carrera', 'postulante_carrera.codigo_carrera', '=', 'carrera.codigo')
            ->leftJoin('grupo', function($j) {
                $j->on('postulante.id_grupo', '=', 'grupo.id')
                ->on('postulante.gestion_grupo', '=', 'grupo.codigo_gestion');
            })
            ->select(
                'datos_personales.ci',
                DB::raw("datos_personales.nombre || ' ' || datos_personales.apellido as nombre_completo"),
                'datos_personales.correo',
                'carrera.nombre as carrera',
                'postulante_carrera.opcion',
                'postulante.estado',
                DB::raw("COALESCE(grupo.id::text || '-' || grupo.nombre_turno, 'Sin grupo') as grupo")
            )
            ->where('postulante_carrera.opcion', 1)
            ->groupBy(
                'postulante.codigo',
                'datos_personales.ci',
                'datos_personales.nombre',
                'datos_personales.apellido',
                'datos_personales.correo',
                'carrera.nombre',
                'postulante_carrera.opcion',
                'postulante.estado',
                'grupo.id',
                'grupo.nombre_turno'
            );

        if ($gestion) $q->where('postulante.gestion_grupo', $gestion);
        if ($carrera)   $q->where('carrera.codigo', $carrera);
        if ($plan)      $q->where('carrera.plan', $plan);
        if ($modalidad) $q->where('carrera.modalidad', $modalidad);

        return [
            'Lista de Postulantes',
            ['CI', 'Nombre Completo', 'Correo', 'Carrera', 'Opción', 'Estado', 'Grupo'],
            $q->orderBy('datos_personales.apellido')->get()
        ];
    }

    private function listaAprobados($gestion, $carrera, $plan, $modalidad): array
    {
        $q = DB::table('postulante')
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->join('postulante_carrera', 'postulante.codigo', '=', 'postulante_carrera.codigo_postulante')
            ->join('carrera', function($j) {
                $j->on('postulante_carrera.codigo_carrera', '=', 'carrera.codigo')
                ->on('postulante_carrera.plan_carrera', '=', 'carrera.plan')
                ->on('postulante_carrera.modalidad_carrera', '=', 'carrera.modalidad');
            })
            ->select(
                'postulante.codigo',
                'datos_personales.ci',
                DB::raw("datos_personales.nombre || ' ' || datos_personales.apellido as nombre_completo"),
                DB::raw("carrera.nombre || CASE WHEN carrera.modalidad = 'virtual' THEN ' (Virtual)' ELSE '' END || ' — ' || CASE WHEN postulante_carrera.opcion IS NULL THEN 'Lista de espera' ELSE 'Opción ' || postulante_carrera.opcion::text END as carrera_asignada"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 1)::numeric, 2) as matematicas"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 2)::numeric, 2) as fisica"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 3)::numeric, 2) as ingles"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 4)::numeric, 2) as computacion"),
            )
            ->where('postulante.estado', 'aprobado')
            ->where('postulante_carrera.asignada', true)
            ->groupBy(
                'postulante.codigo',
                'datos_personales.ci',
                'datos_personales.nombre',
                'datos_personales.apellido',
                'carrera.nombre',
                'postulante_carrera.opcion',
                'carrera.modalidad'
            );

        if ($gestion) $q->where('postulante.gestion_grupo', $gestion);
        if ($carrera)   $q->where('carrera.codigo', $carrera);
        if ($plan)      $q->where('carrera.plan', $plan);
        if ($modalidad) $q->where('carrera.modalidad', $modalidad);

        return [
            'Lista de Aprobados por Carrera',
            ['Código', 'CI', 'Nombre Completo', 'Carrera y Opción Asignada', 'Matemáticas', 'Física', 'Inglés', 'Computación'],
            $q->orderByDesc('postulante.codigo')->get()
        ];
    }

    private function listaReprobados($gestion, $carrera, $plan, $modalidad): array
    {
        $q = DB::table('postulante')
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->join('postulante_carrera', 'postulante.codigo', '=', 'postulante_carrera.codigo_postulante')
            ->join('carrera', function($j) {
                $j->on('postulante_carrera.codigo_carrera', '=', 'carrera.codigo')
                ->on('postulante_carrera.plan_carrera', '=', 'carrera.plan')
                ->on('postulante_carrera.modalidad_carrera', '=', 'carrera.modalidad');
            })
            ->select(
                'postulante.codigo',
                'datos_personales.ci',
                DB::raw("datos_personales.nombre || ' ' || datos_personales.apellido as nombre_completo"),
                DB::raw("carrera.nombre || CASE WHEN carrera.modalidad = 'virtual' THEN ' (Virtual)' ELSE '' END as carrera_elegida"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 1)::numeric, 2) as matematicas"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 2)::numeric, 2) as fisica"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 3)::numeric, 2) as ingles"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 4)::numeric, 2) as computacion"),
            )
            ->where('postulante.estado', 'reprobado')
            ->where('postulante_carrera.opcion', 1)
            ->groupBy('postulante.codigo', 'datos_personales.ci', 'datos_personales.nombre', 'datos_personales.apellido', 'carrera.codigo', 'carrera.nombre', 'carrera.modalidad');

        if ($gestion) $q->where('postulante.gestion_grupo', $gestion);
        if ($carrera)   $q->where('carrera.codigo', $carrera);
        if ($plan)      $q->where('carrera.plan', $plan);
        if ($modalidad) $q->where('carrera.modalidad', $modalidad);

        return [
            'Lista de Reprobados',
            ['Código', 'CI', 'Nombre Completo', 'Carrera Elegida (Primera Opción)', 'Matemáticas', 'Física', 'Inglés', 'Computación'],
            $q->orderByDesc('postulante.codigo')->get()
        ];
    }

    private function promediosPorMateria($gestion): array
    {
        $q = DB::table('examen')
            ->join('materia', 'examen.id_materia', '=', 'materia.id')
            ->join('postulante', 'examen.codigo_postulante', '=', 'postulante.codigo')
            ->select('materia.nombre as materia', DB::raw('ROUND(AVG(examen.nota)::numeric, 2) as promedio'), DB::raw('COUNT(DISTINCT examen.codigo_postulante) as total'));

        if ($gestion) $q->where('postulante.gestion_grupo', $gestion);

        return [
            'Promedios por Materia',
            ['Materia', 'Promedio', 'Total Estudiantes'],
            $q->groupBy('materia.id', 'materia.nombre')->orderBy('materia.nombre')->get()
        ];
    }

    private function estadisticasPorMateria($gestion): array
    {
        $q = DB::table('examen')
            ->join('materia', 'examen.id_materia', '=', 'materia.id')
            ->join('postulante', 'examen.codigo_postulante', '=', 'postulante.codigo')
            ->select(
                'materia.nombre as materia',
                DB::raw('ROUND(AVG(examen.nota)::numeric, 2) as promedio'),
                DB::raw('MAX(examen.nota) as nota_max'),
                DB::raw('MIN(examen.nota) as nota_min'),
                DB::raw('COUNT(DISTINCT CASE WHEN examen.nota >= 60 THEN examen.codigo_postulante END) as aprobados'),
                DB::raw('COUNT(DISTINCT CASE WHEN examen.nota < 60 THEN examen.codigo_postulante END) as reprobados')
            );

        if ($gestion) $q->where('postulante.gestion_grupo', $gestion);

        return [
            'Estadísticas por Materia',
            ['Materia', 'Promedio', 'Máx', 'Mín', 'Aprobados', 'Reprobados'],
            $q->groupBy('materia.id', 'materia.nombre')->orderBy('materia.nombre')->get()
        ];
    }

    private function docentesPorGrupo($gestion, $turno): array
    {
        $q = DB::table('grupo')
            ->join('grupo_materia', function($j) {
                $j->on('grupo.id', '=', 'grupo_materia.id_grupo')
                    ->on('grupo.codigo_gestion', '=', 'grupo_materia.gestion_grupo');
            })
            ->join('personal', 'grupo_materia.registro_personal', '=', 'personal.registro')
            ->join('datos_personales', 'personal.ci', '=', 'datos_personales.ci')
            ->join('materia', 'grupo_materia.id_materia', '=', 'materia.id')
            ->select(
                'grupo.id as grupo',
                'grupo.nombre_turno as turno',
                'grupo.aula',
                'materia.nombre as materia',
                DB::raw("datos_personales.nombre || ' ' || datos_personales.apellido as docente")
            );

        if ($gestion) $q->where('grupo.codigo_gestion', $gestion);
        if ($turno)   $q->where('grupo.nombre_turno', $turno);

        return [
            'Docentes por Grupo',
            ['Grupo', 'Turno', 'Aula', 'Materia', 'Docente'],
            $q->orderBy('grupo.id')->get()
        ];
    }

    private function gruposConMasAprobados($gestion): array
    {
        $q = DB::table('grupo')
            ->join('postulante', function($j) {
                $j->on('postulante.id_grupo', '=', 'grupo.id')
                    ->on('postulante.gestion_grupo', '=', 'grupo.codigo_gestion');
            })
            ->join('examen', 'postulante.codigo', '=', 'examen.codigo_postulante')
            ->select(
                'grupo.id as grupo',
                'grupo.nombre_turno as turno',
                'grupo.aula',
                DB::raw('COUNT(DISTINCT CASE WHEN examen.nota >= 60 THEN postulante.codigo END) as aprobados'),
                DB::raw('COUNT(DISTINCT postulante.codigo) as total')
            );

        if ($gestion) $q->where('grupo.codigo_gestion', $gestion);

        return [
            'Grupos con Más Aprobados',
            ['Grupo', 'Turno', 'Aula', 'Aprobados', 'Total'],
            $q->groupBy('grupo.id', 'grupo.nombre_turno', 'grupo.aula', 'grupo.codigo_gestion')
                ->orderByDesc('aprobados')->get()
        ];
    }

    private function recaudacion($fechaIni, $fechaFin): array
    {
        $q = DB::table('pago')
            ->select(
                'pago.id',
                'pago.concepto',
                'pago.monto',
                'pago.moneda',
                'pago.estado',
                'pago.fecha',
                'pago.id_transaccion'
            )
            ->where('pago.estado', 'completado');

        if ($fechaIni) $q->whereDate('pago.fecha', '>=', $fechaIni);
        if ($fechaFin) $q->whereDate('pago.fecha', '<=', $fechaFin);

        return [
            'Reporte de Recaudación por Pagos',
            ['ID', 'Concepto', 'Monto', 'Moneda', 'Estado', 'Fecha', 'ID Transacción'],
            $q->orderByDesc('pago.fecha')->get()
        ];
    }

    private function promediosGenerales($gestion, $carrera, $plan, $modalidad): array
    {
        $q = DB::table('postulante')
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->join('postulante_carrera', 'postulante.codigo', '=', 'postulante_carrera.codigo_postulante')
            ->join('carrera', 'postulante_carrera.codigo_carrera', '=', 'carrera.codigo')
            ->join('examen', 'postulante.codigo', '=', 'examen.codigo_postulante')
            ->select(
                'datos_personales.ci',
                DB::raw("datos_personales.nombre || ' ' || datos_personales.apellido as nombre_completo"),
                'carrera.nombre as carrera',
                DB::raw('ROUND(AVG(examen.nota)::numeric, 2) as promedio'),
                DB::raw("CASE WHEN AVG(examen.nota) >= 60 THEN 'Aprobado' ELSE 'Reprobado' END as estado")
            )
            ->where('postulante_carrera.opcion', 1);

        if ($gestion) $q->where('postulante.gestion_grupo', $gestion);
        if ($carrera)   $q->where('carrera.codigo', $carrera);
        if ($plan)      $q->where('carrera.plan', $plan);
        if ($modalidad) $q->where('carrera.modalidad', $modalidad);

        return [
            'Promedios Generales',
            ['CI', 'Nombre Completo', 'Carrera', 'Promedio', 'Estado'],
            $q->groupBy('postulante.codigo', 'datos_personales.ci', 'datos_personales.nombre', 'datos_personales.apellido', 'carrera.nombre')
                ->orderBy('datos_personales.apellido')->get()
        ];
    }

    private function gruposHabilitados($gestion, $turno): array
    {
        $q = DB::table('grupo')
            ->leftJoin('carrera_gestion', 'grupo.codigo_gestion', '=', 'carrera_gestion.codigo_gestion')
            ->leftJoin('carrera', 'carrera_gestion.codigo_carrera', '=', 'carrera.codigo')
            ->select(
                'grupo.id',
                'grupo.codigo_gestion as gestion',
                'grupo.nombre_turno as turno',
                'grupo.aula',
                'grupo.total_ins as inscritos',
                DB::raw("COALESCE(carrera.nombre, '-') as carrera")
            );

        if ($gestion) $q->where('grupo.codigo_gestion', $gestion);
        if ($turno)   $q->where('grupo.nombre_turno', $turno);

        return [
            'Grupos Habilitados por Gestión',
            ['ID', 'Gestión', 'Turno', 'Aula', 'Inscritos', 'Carrera'],
            $q->orderBy('grupo.codigo_gestion')->orderBy('grupo.id')->get()
        ];
    }

    private function listaAsistencia($gestion, $materia, $turno): array
    {
        $q = DB::table('asistencia')
            ->join('postulante', function($j) {
                $j->on('asistencia.codigo_postulante', '=', 'postulante.codigo')
                    ->on('asistencia.codigo_gestion', '=', 'postulante.gestion_grupo');
            })
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->join('materia', 'asistencia.id_materia', '=', 'materia.id')
            ->join('grupo', function($j) {
                $j->on('asistencia.id_grupo', '=', 'grupo.id')
                    ->on('asistencia.codigo_gestion', '=', 'grupo.codigo_gestion');
            })
            ->select(
                'asistencia.fecha',
                DB::raw("datos_personales.nombre || ' ' || datos_personales.apellido as nombre_completo"),
                'materia.nombre as materia',
                'grupo.nombre_turno as turno',
                DB::raw("CASE WHEN asistencia.presente THEN 'Presente' ELSE 'Ausente' END as asistencia")
            );

        if ($gestion) $q->where('asistencia.codigo_gestion', $gestion);
        if ($materia) $q->where('asistencia.id_materia', $materia);
        if ($turno)   $q->where('grupo.nombre_turno', $turno);

        return [
            'Lista de Asistencia',
            ['Fecha', 'Nombre Completo', 'Materia', 'Turno', 'Asistencia'],
            $q->orderByDesc('asistencia.fecha')->orderBy('datos_personales.apellido')->get()
        ];
    }

    private function nombreArchivo($tipo, $ext): string
    {
        return 'reporte_' . $tipo . '_' . now()->format('Ymd_His') . '.' . $ext;
    }
}
