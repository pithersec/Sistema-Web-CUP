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
    // =========================================================
    // VISTA PRINCIPAL — muestra filtros y tabla paginada
    // =========================================================
    public function index(Request $request)
    {
        // Datos para los dropdowns de filtros
        $gestiones = Gestion::orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")->get();
        $carreras  = Carrera::orderBy('modalidad')->orderBy('nombre')->get();
        $materias  = DB::table('materia')->orderBy('nombre')->get();
        $turnos    = DB::table('turno')->orderByRaw("CASE WHEN nombre='mañana' THEN 0 WHEN nombre='tarde' THEN 1 ELSE 2 END")->get();

        // Descomponer carrera en sus 3 partes de la PK compuesta (codigo|plan|modalidad)
        $carrera          = $request->input('carrera', '');
        $carreraParts     = $carrera ? explode('|', $carrera) : [];
        $codigoCarrera    = $carreraParts[0] ?? null;
        $planCarrera      = $carreraParts[1] ?? null;
        $modalidadCarrera = $carreraParts[2] ?? null;

        // Filtros activos
        $tipoActual  = $request->input('tipo_reporte', '');
        $gestion     = $request->input('gestion', '');
        $turno       = $request->input('turno', '');
        $materia     = $request->input('materia', '');
        $fechaInicio = $request->input('fecha_inicio', '');
        $fechaFin    = $request->input('fecha_fin', '');
        $estado      = $request->input('estado', '');

        // Valores por defecto
        $titulo     = '';
        $columnas   = [];
        $filas      = collect();
        $resumen    = '';
        $totalFilas = 0;

        if ($tipoActual) {
            [$titulo, $columnas, $todasFilas, $resumen] = $this->obtenerDatos(
                $tipoActual, $gestion, $codigoCarrera, $planCarrera, $modalidadCarrera,
                $turno, $materia, $fechaInicio, $fechaFin, $estado
            );

            // Paginación en memoria — 15 registros por página
            $totalFilas = $todasFilas->count();
            $page       = $request->input('page', 1);
            $filas      = new \Illuminate\Pagination\LengthAwarePaginator(
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
            'fechaInicio', 'fechaFin', 'titulo', 'columnas', 'filas',
            'totalFilas', 'estado', 'resumen'
        ));
    }

    // =========================================================
    // EXPORTAR — descarga PDF o Excel sin paginación
    // =========================================================
    public function exportar(Request $request)
    {
        $tipo     = $request->input('tipo_reporte');
        $gestion  = $request->input('gestion');
        $formato  = $request->input('formato', 'pdf');
        $estado   = $request->input('estado', '');

        // Descomponer carrera igual que en index()
        $carrera          = $request->input('carrera', '');
        $carreraParts     = $carrera ? explode('|', $carrera) : [];
        $codigoCarrera    = $carreraParts[0] ?? null;
        $planCarrera      = $carreraParts[1] ?? null;
        $modalidadCarrera = $carreraParts[2] ?? null;

        $turno    = $request->input('turno');
        $materia  = $request->input('materia');
        $fechaIni = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        [$titulo, $columnas, $filas, $resumen] = $this->obtenerDatos(
            $tipo, $gestion, $codigoCarrera, $planCarrera, $modalidadCarrera,
            $turno, $materia, $fechaIni, $fechaFin, $estado
        );

        if ($formato === 'excel') {
            return Excel::download(
                new ReporteExport($titulo, $columnas, $filas, $resumen),
                $this->nombreArchivo($tipo, 'xlsx')
            );
        }

        // PDF: límite de 1000 filas para evitar timeout de dompdf
        $total   = $filas->count();
        $cortado = $total > 1000;
        $filas   = $filas->take(1000);

        $pdf = Pdf::loadView('reportes.pdf', compact('titulo', 'columnas', 'filas', 'total', 'cortado', 'resumen'))
            ->setPaper('a4', 'landscape')
            ->setOption('dpi', 96)
            ->setOption('default-font-size', 9);

        return $pdf->download($this->nombreArchivo($tipo, 'pdf'));
    }

    // =========================================================
    // ROUTER — delega al método correcto según tipo de reporte
    // =========================================================
    private function obtenerDatos($tipo, $gestion, $carrera, $plan, $modalidad, $turno, $materia, $fechaIni, $fechaFin, $estado): array
    {
        return match($tipo) {
            'postulantes'          => $this->listaPostulantes($gestion, $carrera, $plan, $modalidad, $estado),
            'aprobados'            => $this->listaAprobados($gestion, $carrera, $plan, $modalidad),
            'reprobados'           => $this->listaReprobados($gestion, $carrera, $plan, $modalidad),
            'estadisticas_materia' => $this->estadisticasPorMateria($gestion),
            'docentes_grupo'       => $this->docentesPorGrupo($gestion, $turno),
            'grupos_aprobados'     => $this->gruposConMasAprobados($gestion, $turno),
            'recaudacion'          => $this->recaudacion($fechaIni, $fechaFin),
            'promedios_generales'  => $this->promediosGenerales($gestion, $estado),
            'grupos_habilitados'   => $this->gruposHabilitados($gestion, $turno),
            'asistencia'           => $this->listaAsistencia($gestion, $materia, $turno),
            default                => ['Reporte desconocido', [], collect(), ''],
        };
    }

    // =========================================================
    // REPORTE 1: LISTA DE POSTULANTES
    // Muestra todos los postulantes con su carrera:
    //   - Si está aprobado → carrera asignada (asignada=true)
    //   - Si no → carrera de primera opción
    // =========================================================
    private function listaPostulantes($gestion, $carrera, $plan, $modalidad, $estado): array
    {
        $q = DB::table('postulante')
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->leftJoin('grupo', function($j) {
                $j->on('postulante.id_grupo', '=', 'grupo.id')
                  ->on('postulante.gestion_grupo', '=', 'grupo.codigo_gestion');
            })
            ->select(
                'postulante.codigo',
                'datos_personales.ci',
                DB::raw("datos_personales.nombre || ' ' || datos_personales.apellido as nombre_completo"),
                'datos_personales.correo',
                // Carrera dinámica: asignada para aprobados, opción 1 para el resto
                DB::raw("CASE
                    WHEN postulante.estado = 'aprobado' THEN (
                        SELECT c2.nombre || CASE WHEN c2.modalidad = 'virtual' THEN ' (Virtual)' ELSE '' END
                        FROM postulante_carrera pc2
                        JOIN carrera c2 ON pc2.codigo_carrera = c2.codigo
                            AND pc2.plan_carrera = c2.plan
                            AND pc2.modalidad_carrera = c2.modalidad
                        WHERE pc2.codigo_postulante = postulante.codigo
                        AND pc2.asignada = true
                        LIMIT 1
                    )
                    ELSE (
                        SELECT c2.nombre || CASE WHEN c2.modalidad = 'virtual' THEN ' (Virtual)' ELSE '' END
                        FROM postulante_carrera pc2
                        JOIN carrera c2 ON pc2.codigo_carrera = c2.codigo
                            AND pc2.plan_carrera = c2.plan
                            AND pc2.modalidad_carrera = c2.modalidad
                        WHERE pc2.codigo_postulante = postulante.codigo
                        AND pc2.opcion = 1
                        LIMIT 1
                    )
                END as carrera"),
                DB::raw("INITCAP(postulante.estado) as estado"),
                DB::raw("COALESCE(grupo.id::text || ' - ' || grupo.nombre_turno, 'Sin grupo') as grupo")
            );

        if ($gestion) $q->where('postulante.gestion_grupo', $gestion);
        if ($estado)  $q->where('postulante.estado', $estado);

        // Filtro por carrera via subquery (no hay join directo con carrera en este query)
        if ($carrera) {
            $q->whereExists(function($sub) use ($carrera, $plan, $modalidad) {
                $sub->select(DB::raw(1))
                    ->from('postulante_carrera as pc3')
                    ->whereColumn('pc3.codigo_postulante', 'postulante.codigo')
                    ->where('pc3.codigo_carrera', $carrera)
                    ->where('pc3.plan_carrera', $plan)
                    ->where('pc3.modalidad_carrera', $modalidad)
                    ->where('pc3.opcion', 1);
            });
        }

        $filas = $q->orderByRaw("SUBSTRING(postulante.codigo, 2, 4) DESC")
                   ->orderByRaw("SUBSTRING(postulante.codigo, 1, 1) DESC")
                   ->orderByDesc('postulante.codigo')
                   ->get();

        $resumen = 'Total de postulantes: ' . number_format($filas->count());

        return [
            'Lista de Postulantes',
            ['Código', 'CI', 'Nombre Completo', 'Correo', 'Carrera', 'Estado', 'Grupo'],
            $filas,
            $resumen,
        ];
    }

    // =========================================================
    // REPORTE 2: APROBADOS POR CARRERA
    // Solo postulantes con estado=aprobado y asignada=true
    // Muestra notas finales por materia (N1×30% + N2×30% + N3×40%)
    // =========================================================
    private function listaAprobados($gestion, $carrera, $plan, $modalidad): array
    {
        $q = DB::table('postulante')
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->join('postulante_carrera', 'postulante.codigo', '=', 'postulante_carrera.codigo_postulante')
            // Join con PK compuesta de carrera para evitar duplicados
            ->join('carrera', function($j) {
                $j->on('postulante_carrera.codigo_carrera', '=', 'carrera.codigo')
                  ->on('postulante_carrera.plan_carrera', '=', 'carrera.plan')
                  ->on('postulante_carrera.modalidad_carrera', '=', 'carrera.modalidad');
            })
            ->select(
                'postulante.codigo',
                'datos_personales.ci',
                DB::raw("datos_personales.nombre || ' ' || datos_personales.apellido as nombre_completo"),
                // Carrera + modalidad + vía de ingreso (opción 1, 2 o lista de espera)
                DB::raw("carrera.nombre || CASE WHEN carrera.modalidad = 'virtual' THEN ' (Virtual)' ELSE '' END
                    || ' — ' || CASE WHEN postulante_carrera.opcion IS NULL THEN 'Lista de espera'
                    ELSE 'Opción ' || postulante_carrera.opcion::text END as carrera_asignada"),
                // Notas finales por materia via subqueries correlacionadas
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 1)::numeric, 2) as matematicas"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 2)::numeric, 2) as fisica"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 3)::numeric, 2) as ingles"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 4)::numeric, 2) as computacion"),
            )
            ->where('postulante.estado', 'aprobado')
            ->where('postulante_carrera.asignada', true)
            ->groupBy(
                'postulante.codigo', 'datos_personales.ci', 'datos_personales.nombre',
                'datos_personales.apellido', 'carrera.nombre', 'postulante_carrera.opcion', 'carrera.modalidad'
            );

        if ($gestion)  $q->where('postulante.gestion_grupo', $gestion);
        if ($carrera)  $q->where('carrera.codigo', $carrera);
        if ($plan)     $q->where('carrera.plan', $plan);
        if ($modalidad) $q->where('carrera.modalidad', $modalidad);

        $filas   = $q->orderByRaw("SUBSTRING(postulante.codigo, 2, 4) DESC")
                     ->orderByRaw("SUBSTRING(postulante.codigo, 1, 1) DESC")
                     ->orderByDesc('postulante.codigo')
                     ->get();

        $resumen = 'Total aprobados: ' . number_format($filas->count());

        return [
            'Lista de Aprobados por Carrera',
            ['Código', 'CI', 'Nombre Completo', 'Carrera y Opción Asignada', 'Matemáticas', 'Física', 'Inglés', 'Computación'],
            $filas,
            $resumen,
        ];
    }

    // =========================================================
    // REPORTE 3: LISTA DE REPROBADOS
    // Muestra carrera de primera opción (no asignada, ya que reprobaron)
    // Notas finales por materia igual que en aprobados
    // =========================================================
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
            ->groupBy(
                'postulante.codigo', 'datos_personales.ci', 'datos_personales.nombre',
                'datos_personales.apellido', 'carrera.codigo', 'carrera.nombre', 'carrera.modalidad'
            );

        if ($gestion)  $q->where('postulante.gestion_grupo', $gestion);
        if ($carrera)  $q->where('carrera.codigo', $carrera);
        if ($plan)     $q->where('carrera.plan', $plan);
        if ($modalidad) $q->where('carrera.modalidad', $modalidad);

        $filas   = $q->orderByRaw("SUBSTRING(postulante.codigo, 2, 4) DESC")
                     ->orderByRaw("SUBSTRING(postulante.codigo, 1, 1) DESC")
                     ->orderByDesc('postulante.codigo')
                     ->get();

        $resumen = 'Total reprobados: ' . number_format($filas->count());

        return [
            'Lista de Reprobados',
            ['Código', 'CI', 'Nombre Completo', 'Carrera Elegida (Primera Opción)', 'Matemáticas', 'Física', 'Inglés', 'Computación'],
            $filas,
            $resumen,
        ];
    }

    // =========================================================
    // REPORTE 4: ESTADÍSTICAS POR MATERIA
    // Usa subquery como tabla derivada para calcular nota final
    // correctamente ponderada antes de agregar
    // =========================================================
    private function estadisticasPorMateria($gestion): array
    {
        // Subquery: calcula nota final por alumno por materia
        $q = DB::table(DB::raw('(
            SELECT e.id_materia, e.codigo_postulante,
                   SUM(e.nota * e.ponderacion / 100.0) as nota_final
            FROM examen e
            JOIN postulante p ON e.codigo_postulante = p.codigo
            ' . ($gestion ? "WHERE p.gestion_grupo = '$gestion'" : '') . '
            GROUP BY e.id_materia, e.codigo_postulante
        ) as notas_finales'))
            ->join('materia', 'notas_finales.id_materia', '=', 'materia.id')
            ->select(
                'materia.nombre as materia',
                DB::raw('ROUND(AVG(notas_finales.nota_final)::numeric, 2) as promedio'),
                DB::raw('ROUND(MAX(notas_finales.nota_final)::numeric, 2) as nota_max'),
                DB::raw('ROUND(MIN(notas_finales.nota_final)::numeric, 2) as nota_min'),
                DB::raw('COUNT(DISTINCT CASE WHEN notas_finales.nota_final >= 60 THEN notas_finales.codigo_postulante END) as aprobados'),
                DB::raw('COUNT(DISTINCT CASE WHEN notas_finales.nota_final < 60 THEN notas_finales.codigo_postulante END) as reprobados'),
                DB::raw('COUNT(DISTINCT notas_finales.codigo_postulante) as total')
            )
            ->groupBy('materia.id', 'materia.nombre')
            ->orderBy('materia.nombre');

        $filas = $q->get();

        return [
            'Estadísticas por Materia',
            ['Materia', 'Promedio Final', 'Nota Máxima', 'Nota Mínima', 'Aprobados', 'Reprobados', 'Total'],
            $filas,
            '',  // sin resumen — el reporte ya es un resumen
        ];
    }

    // =========================================================
    // REPORTE 5: DOCENTES POR GRUPO
    // Ordenado por gestión desc, turno (mañana→tarde→noche), grupo id
    // =========================================================
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
                'grupo.codigo_gestion as gestion',
                'grupo.id as grupo',
                DB::raw("INITCAP(grupo.nombre_turno) as turno"),
                'grupo.aula',
                'materia.nombre as materia',
                'personal.registro',
                DB::raw("datos_personales.nombre || ' ' || datos_personales.apellido as docente")
            );

        if ($gestion) $q->where('grupo.codigo_gestion', $gestion);
        if ($turno)   $q->where('grupo.nombre_turno', $turno);

        $filas = $q->orderByRaw("SPLIT_PART(grupo.codigo_gestion, '-', 2) DESC, SPLIT_PART(grupo.codigo_gestion, '-', 1) DESC")
                   ->orderByRaw("CASE WHEN grupo.nombre_turno = 'mañana' THEN 0 WHEN grupo.nombre_turno = 'tarde' THEN 1 ELSE 2 END")
                   ->orderBy('grupo.id')
                   ->get();

        // Contar docentes únicos
        $totalDocentes = $filas->pluck('registro')->unique()->count();
        $resumen = 'Total docentes activos: ' . $totalDocentes;

        return [
            'Docentes por Grupo',
            ['Gestión', 'Grupo', 'Turno', 'Aula', 'Materia', 'Registro', 'Docente'],
            $filas,
            $resumen,
        ];
    }

    // =========================================================
    // REPORTE 6: GRUPOS CON MÁS APROBADOS
    // Usa postulante.estado='aprobado' para contar correctamente
    // Incluye porcentaje respecto al total del grupo
    // =========================================================
    private function gruposConMasAprobados($gestion, $turno): array
    {
        $q = DB::table('grupo')
            ->join('postulante', function($j) {
                $j->on('postulante.id_grupo', '=', 'grupo.id')
                  ->on('postulante.gestion_grupo', '=', 'grupo.codigo_gestion');
            })
            ->join('examen', 'postulante.codigo', '=', 'examen.codigo_postulante')
            ->select(
                'grupo.codigo_gestion as gestion',
                'grupo.id as grupo',
                DB::raw("INITCAP(grupo.nombre_turno) as turno"),
                'grupo.aula',
                DB::raw("COUNT(DISTINCT CASE WHEN postulante.estado = 'aprobado' THEN postulante.codigo END) as aprobados"),
                DB::raw("COUNT(DISTINCT postulante.codigo) as total"),
                DB::raw("ROUND((COUNT(DISTINCT CASE WHEN postulante.estado = 'aprobado' THEN postulante.codigo END)::numeric / NULLIF(COUNT(DISTINCT postulante.codigo), 0) * 100), 1)::text || '%' as porcentaje")
            )
            ->groupBy('grupo.id', 'grupo.nombre_turno', 'grupo.aula', 'grupo.codigo_gestion')
            ->orderByDesc('aprobados');

        if ($gestion) $q->where('grupo.codigo_gestion', $gestion);
        if ($turno)   $q->where('grupo.nombre_turno', $turno);

        $filas = $q->get();

        $totalAprobados = $filas->sum('aprobados');
        $promedioPorc   = $filas->count() > 0
            ? number_format($filas->avg(fn($f) => (float) rtrim($f->porcentaje, '%')), 1) . '%'
            : '0%';
        $resumen = 'Total aprobados: ' . number_format($totalAprobados) . ' · Promedio de aprobación: ' . $promedioPorc;

        return [
            'Grupos con Más Aprobados',
            ['Gestión', 'Grupo', 'Turno', 'Aula', 'Aprobados', 'Total', 'Porcentaje Respecto al Total'],
            $filas,
            $resumen,
        ];
    }

    // =========================================================
    // REPORTE 7: REGISTRO DE PAGOS
    // Muestra pagos completados con datos del postulante
    // Monto fusionado con moneda (ej: USD 700.00)
    // =========================================================
    private function recaudacion($fechaIni, $fechaFin): array
    {
        $q = DB::table('pago')
            ->join('postulante', 'postulante.id_pago', '=', 'pago.id')
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->select(
                'postulante.codigo',
                DB::raw("datos_personales.nombre || ' ' || datos_personales.apellido as nombre_completo"),
                'pago.id',
                'pago.concepto',
                DB::raw("pago.moneda || ' ' || pago.monto::text as monto"),
                DB::raw("TO_CHAR(pago.fecha, 'DD/MM/YYYY HH24:MI:SS') as fecha"),
            )
            ->where('pago.estado', 'completado');

        if ($fechaIni) $q->whereDate('pago.fecha', '>=', $fechaIni);
        if ($fechaFin) $q->whereDate('pago.fecha', '<=', $fechaFin);

        $filas = $q->orderByDesc('pago.fecha')->get();

        // Calcular totales por moneda
        $totales = DB::table('pago')
            ->where('estado', 'completado')
            ->when($fechaIni, fn($q) => $q->whereDate('fecha', '>=', $fechaIni))
            ->when($fechaFin, fn($q) => $q->whereDate('fecha', '<=', $fechaFin))
            ->select('moneda', DB::raw('SUM(monto) as total'))
            ->groupBy('moneda')
            ->get()
            ->map(fn($r) => $r->moneda . ' ' . number_format($r->total, 2))
            ->join(' · ');

        $resumen = 'Total de pagos: ' . number_format($filas->count()) . ' · Recaudación: ' . $totales;

        return [
            'Registro de Pagos',
            ['Código', 'Nombre Completo', 'ID Pago', 'Concepto', 'Monto', 'Fecha'],
            $filas,
            $resumen,
        ];
    }

    // =========================================================
    // REPORTE 8: PROMEDIOS GENERALES
    // Incluye aprobados, reprobados e inscritos de gestiones cerradas
    // Excluye inscritos de la gestión activa (sin exámenes completos)
    // La gestión activa se detecta dinámicamente (la más reciente)
    // =========================================================
    private function promediosGenerales($gestion, $estado): array
    {
        // Detectar gestión activa dinámicamente sin hardcodear
        $gestionActiva = DB::table('gestion')
            ->orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")
            ->value('codigo');

        $q = DB::table('postulante')
            ->join('datos_personales', 'postulante.ci', '=', 'datos_personales.ci')
            ->select(
                'postulante.codigo',
                'datos_personales.ci',
                DB::raw("datos_personales.nombre || ' ' || datos_personales.apellido as nombre_completo"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 1)::numeric, 2) as matematicas"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 2)::numeric, 2) as fisica"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 3)::numeric, 2) as ingles"),
                DB::raw("ROUND((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 4)::numeric, 2) as computacion"),
                // Promedio final = promedio de las 4 notas finales por materia
                DB::raw("ROUND(((
                    COALESCE((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 1), 0) +
                    COALESCE((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 2), 0) +
                    COALESCE((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 3), 0) +
                    COALESCE((SELECT SUM(e2.nota * e2.ponderacion / 100.0) FROM examen e2 WHERE e2.codigo_postulante = postulante.codigo AND e2.id_materia = 4), 0)
                ) / 4.0)::numeric, 2) as promedio_final"),
                DB::raw("INITCAP(postulante.estado) as estado")
            )
            ->where(function($q) use ($gestionActiva) {
                $q->where('postulante.estado', 'aprobado')
                  ->orWhere('postulante.estado', 'reprobado')
                  ->orWhere(function($q2) use ($gestionActiva) {
                      $q2->where('postulante.estado', 'inscrito')
                         ->where('postulante.gestion_grupo', '!=', $gestionActiva);
                  });
            });

        if ($gestion) $q->where('postulante.gestion_grupo', $gestion);
        if ($estado)  $q->where('postulante.estado', $estado);

        $filas = $q->orderByRaw("SUBSTRING(postulante.codigo, 2, 4) DESC")
                   ->orderByRaw("SUBSTRING(postulante.codigo, 1, 1) DESC")
                   ->orderByDesc('postulante.codigo')
                   ->get();

        $promedio = $filas->avg('promedio_final');
        $resumen  = 'Total: ' . number_format($filas->count()) . ' · Promedio general: ' . number_format($promedio, 2);

        return [
            'Promedios Generales',
            ['Código', 'CI', 'Nombre Completo', 'Matemáticas', 'Física', 'Inglés', 'Computación', 'Promedio Final', 'Estado'],
            $filas,
            $resumen,
        ];
    }

    // =========================================================
    // REPORTE 9: GRUPOS HABILITADOS POR GESTIÓN
    // Turno fusionado con horario: Mañana · 07:00 - 11:00
    // =========================================================
    private function gruposHabilitados($gestion, $turno): array
    {
        $q = DB::table('grupo')
            ->join('turno', 'grupo.nombre_turno', '=', 'turno.nombre')
            ->leftJoin('grupo_materia', function($j) {
                $j->on('grupo.id', '=', 'grupo_materia.id_grupo')
                  ->on('grupo.codigo_gestion', '=', 'grupo_materia.gestion_grupo');
            })
            ->select(
                'grupo.id',
                'grupo.codigo_gestion as gestion',
                DB::raw("INITCAP(grupo.nombre_turno) || ' · ' || TO_CHAR(turno.hora_inicio, 'HH24:MI') || ' - ' || TO_CHAR(turno.hora_fin, 'HH24:MI') as turno"),
                'grupo.aula',
                'grupo.total_ins as inscritos',
                DB::raw("COUNT(DISTINCT grupo_materia.registro_personal) as docentes")
            )
            ->groupBy(
                'grupo.id', 'grupo.codigo_gestion', 'grupo.nombre_turno',
                'grupo.aula', 'grupo.total_ins', 'turno.hora_inicio', 'turno.hora_fin'
            );

        if ($gestion) $q->where('grupo.codigo_gestion', $gestion);
        if ($turno)   $q->where('grupo.nombre_turno', $turno);

        $filas = $q->orderByRaw("SPLIT_PART(grupo.codigo_gestion, '-', 2) DESC, SPLIT_PART(grupo.codigo_gestion, '-', 1) DESC")
                   ->orderByRaw("CASE WHEN grupo.nombre_turno = 'mañana' THEN 0 WHEN grupo.nombre_turno = 'tarde' THEN 1 ELSE 2 END")
                   ->orderBy('grupo.id')
                   ->get();

        $totalGrupos    = $filas->count();
        $totalInscritos = $filas->sum('inscritos');
        $resumen = 'Total grupos: ' . $totalGrupos . ' · Total inscritos: ' . number_format($totalInscritos);

        return [
            'Grupos Habilitados por Gestión',
            ['ID', 'Gestión', 'Turno', 'Aula', 'Cant. Inscritos', 'Cant. Docentes'],
            $filas,
            $resumen,
        ];
    }

    // =========================================================
    // REPORTE 10: LISTA DE ASISTENCIA
    // Sin datos hasta implementar CU-20
    // =========================================================
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
                'postulante.codigo',
                DB::raw("datos_personales.nombre || ' ' || datos_personales.apellido as nombre_completo"),
                'grupo.id as grupo',
                'materia.nombre as materia',
                DB::raw("INITCAP(grupo.nombre_turno) as turno"),
                DB::raw("TO_CHAR(asistencia.fecha, 'DD/MM/YYYY') as fecha"),
                DB::raw("CASE WHEN asistencia.presente THEN 'Presente' ELSE 'Ausente' END as asistencia")
            );

        if ($gestion) $q->where('asistencia.codigo_gestion', $gestion);
        if ($materia) $q->where('asistencia.id_materia', $materia);
        if ($turno)   $q->where('grupo.nombre_turno', $turno);

        $filas   = $q->orderByDesc('asistencia.fecha')->orderBy('datos_personales.apellido')->get();
        $resumen = 'Total registros: ' . number_format($filas->count());

        return [
            'Lista de Asistencia',
            ['Código', 'Nombre Completo', 'Grupo', 'Materia', 'Turno', 'Fecha', 'Asistencia'],
            $filas,
            $resumen,
        ];
    }

    // =========================================================
    // HELPER — nombre de archivo para descarga
    // =========================================================
    private function nombreArchivo($tipo, $ext): string
    {
        return 'reporte_' . $tipo . '_' . now()->format('Ymd_His') . '.' . $ext;
    }
}