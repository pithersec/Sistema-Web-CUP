<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParametroController extends Controller
{
    // =========================================================
    // Gestión activa = la más reciente por código
    // =========================================================
    private function getGestionActiva()
    {
        return DB::table('gestion')
            ->orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")
            ->first();
    }

    private function registrarBitacora(string $accion): void
    {
        Bitacora::create([
            'ip'         => request()->ip(),
            'accion'     => $accion,
            'fecha_hora' => now(),
            'id_usuario' => Auth::id(),
        ]);
    }

    // =========================================================
    // CU-18: mostrarParametros()
    // =========================================================
    public function mostrarParametros()
    {
        $gestion  = $this->getGestionActiva();
        $turnos   = DB::table('turno')
            ->orderByRaw("CASE WHEN nombre='mañana' THEN 0 WHEN nombre='tarde' THEN 1 ELSE 2 END")
            ->get();
        $materias = DB::table('materia')->orderBy('id')->get();

        $gestionCerrada = $gestion && now()->toDateString() > $gestion->fecha_fin;

        $siguienteCodigo  = '';
        $gestionProcesada = false;

        if ($gestion) {
            $gestionCerrada = now()->toDateString() > $gestion->fecha_fin;
            $partes = explode('-', $gestion->codigo);
            $semestre = (int)$partes[0];
            $anio     = (int)$partes[1];
            $siguienteCodigo = $semestre === 1 ? '2-' . $anio : '1-' . ($anio + 1);
            $gestionCorta = str_replace('-', '', $gestion->codigo);
            $gestionProcesada = $gestionCerrada && !DB::table('postulante')
                ->where('codigo', 'LIKE', $gestionCorta . '%')
                ->where('estado', 'inscrito')
                ->exists();
        } else {
            $gestionCerrada = false;
        }

        return view('parametros.index', compact('gestion', 'turnos', 'materias', 'gestionCerrada', 'siguienteCodigo', 'gestionProcesada'));
    }

    // =========================================================
    // CU-18: modificarParametros()
    // Orden de operaciones:
    //   1. Validar fechas
    //   2. Si cambia hora_fin turno → redistribuir duraciones equitativamente
    //   3. Calcular horas proyectadas de grupo_materia
    //   4. Validar que turnos no se solapen
    //   5. Guardar todo en transacción
    // =========================================================
    public function modificarParametros(Request $request)
    {
        $gestion = $this->getGestionActiva();
        if (!$gestion) return back()->with('error', 'No hay una gestión activa.');

        // Validación básica
        $request->validate([
            'fecha_ini'            => 'required|date',
            'fecha_fin'            => 'required|date|after:fecha_ini',
            'fecha_inicio_notas'   => 'required|date|after_or_equal:fecha_ini|before_or_equal:fecha_fin',
            'fecha_fin_notas'      => 'required|date|after:fecha_inicio_notas|before_or_equal:fecha_fin',
            'nota_minima'          => 'required|integer|min:0|max:100',
            'turnos.*.hora_inicio' => 'required|date_format:H:i',
            'turnos.*.hora_fin'    => 'required|date_format:H:i',
            'materias.*.duracion'  => 'required|numeric|min:0.5|max:8',
        ]);

        $turnosInput   = $request->turnos;   // [mañana => [hora_inicio, hora_fin], ...]
        $materiasInput = $request->materias; // [id => [duracion], ...]

        // Prioridad para redistribución de duraciones: mat(1), fis(2), com(4), ing(3)
        $prioridadMaterias = [1, 2, 4, 3];

        // --------------------------------------------------
        // Si cambia hora_fin de algún turno → redistribuir duraciones
        // --------------------------------------------------
        $turnosDB = DB::table('turno')->get()->keyBy('nombre');
        foreach ($turnosInput as $nombre => $datos) {
            $turnoAnterior = $turnosDB[$nombre] ?? null;
            if (!$turnoAnterior) continue;

            $horaIniNueva = Carbon::createFromFormat('H:i', $datos['hora_inicio']);
            $horaFinNueva = Carbon::createFromFormat('H:i', $datos['hora_fin']);
            $horaFinAnterior = Carbon::createFromFormat('H:i:s', $turnoAnterior->hora_fin);

            // Si cambió la hora_fin, redistribuir duraciones equitativamente
            if ($horaFinNueva->format('H:i') !== $horaFinAnterior->format('H:i')) {
                $totalHoras = $horaFinNueva->diffInMinutes($horaIniNueva) / 60.0;
                $base       = floor(($totalHoras / 4) * 2) / 2; // redondear a 0.5 inferior
                $sobrante   = round($totalHoras - ($base * 3), 1);

                // Asignar base a las 3 primeras materias por prioridad, sobrante a inglés
                foreach ($prioridadMaterias as $idx => $idMateria) {
                    $duracion = ($idx < 3) ? $base : $sobrante;
                    $materiasInput[$idMateria]['duracion'] = max(0.5, $duracion);
                }
            }
        }

        // --------------------------------------------------
        // Calcular horas proyectadas por turno para validación
        // --------------------------------------------------
        $finProyectado = [];
        foreach ($turnosInput as $nombre => $datos) {
            $horaActual = Carbon::createFromFormat('H:i', $datos['hora_inicio']);
            foreach ($prioridadMaterias as $idMateria) {
                $duracion   = (float)($materiasInput[$idMateria]['duracion'] ?? 1.0);
                $horaActual = $horaActual->addMinutes((int)($duracion * 60));
            }
            $finProyectado[$nombre] = $horaActual->format('H:i');
        }

        // Validar que hora_inicio < hora_fin por turno
        foreach ($turnosInput as $nombre => $datos) {
            $ini = Carbon::createFromFormat('H:i', $datos['hora_inicio']);
            $fin = Carbon::createFromFormat('H:i', $datos['hora_fin']);
            if ($ini->gte($fin)) {
                return back()->withErrors(['turnos' => "El turno {$nombre}: hora inicio debe ser menor a hora fin."])->withInput();
            }
        }

        // Validar que turnos no se solapen (usando fin proyectado real)
        $finManana = Carbon::createFromFormat('H:i', $finProyectado['mañana'] ?? '00:00');
        $iniTarde  = Carbon::createFromFormat('H:i', $turnosInput['tarde']['hora_inicio'] ?? '00:00');
        $finTarde  = Carbon::createFromFormat('H:i', $finProyectado['tarde'] ?? '00:00');
        $iniNoche  = Carbon::createFromFormat('H:i', $turnosInput['noche']['hora_inicio'] ?? '00:00');

        if ($finManana->gt($iniTarde)) {
            return back()->withErrors(['turnos' => "El turno mañana termina a las {$finProyectado['mañana']} y choca con el inicio de tarde ({$turnosInput['tarde']['hora_inicio']})."])->withInput();
        }
        if ($finTarde->gt($iniNoche)) {
            return back()->withErrors(['turnos' => "El turno tarde termina a las {$finProyectado['tarde']} y choca con el inicio de noche ({$turnosInput['noche']['hora_inicio']})."])->withInput();
        }

        // --------------------------------------------------
        // Guardar todo en transacción
        // --------------------------------------------------
        DB::transaction(function() use ($request, $gestion, $turnosInput, $materiasInput, $prioridadMaterias) {
            // 1. Actualizar gestión
            DB::table('gestion')->where('codigo', $gestion->codigo)->update([
                'fecha_ini'          => $request->fecha_ini,
                'fecha_fin'          => $request->fecha_fin,
                'fecha_inicio_notas' => $request->fecha_inicio_notas,
                'fecha_fin_notas'    => $request->fecha_fin_notas,
                'nota_minima'        => $request->nota_minima,
            ]);

            // 2. Actualizar turnos
            foreach ($turnosInput as $nombre => $datos) {
                DB::table('turno')->where('nombre', $nombre)->update([
                    'hora_inicio' => $datos['hora_inicio'] . ':00',
                    'hora_fin'    => $datos['hora_fin'] . ':00',
                ]);
            }

            // 3. Actualizar duraciones de materias
            foreach ($materiasInput as $id => $datos) {
                DB::table('materia')->where('id', $id)->update([
                    'duracion' => $datos['duracion'],
                ]);
            }

            // 4. Recalcular grupo_materia para la gestión activa
            $grupos           = DB::table('grupo')->where('codigo_gestion', $gestion->codigo)->get();
            $turnosActualizados   = DB::table('turno')->get()->keyBy('nombre');
            $materiasActualizadas = DB::table('materia')->get()->keyBy('id');

            foreach ($grupos as $grupo) {
                $turno = $turnosActualizados[$grupo->nombre_turno] ?? null;
                if (!$turno) continue;

                $grupoMaterias = DB::table('grupo_materia')
                    ->where('id_grupo', $grupo->id)
                    ->where('gestion_grupo', $gestion->codigo)
                    ->orderBy('orden')
                    ->get();

                $horaActual = Carbon::createFromFormat('H:i:s', $turno->hora_inicio);

                foreach ($grupoMaterias as $gm) {
                    $materia  = $materiasActualizadas[$gm->id_materia] ?? null;
                    $duracion = $materia ? (float)$materia->duracion : 1.0;

                    $horaInicio = $horaActual->copy();
                    $horaFin    = $horaActual->copy()->addMinutes((int)($duracion * 60));

                    DB::table('grupo_materia')
                        ->where('id_grupo', $gm->id_grupo)
                        ->where('gestion_grupo', $gm->gestion_grupo)
                        ->where('id_materia', $gm->id_materia)
                        ->update([
                            'hora_inicio' => $horaInicio->format('H:i:s'),
                            'hora_fin'    => $horaFin->format('H:i:s'),
                        ]);

                    $horaActual = $horaFin;
                }
            }

            $this->registrarBitacora("Modificación de parámetros — Gestión {$gestion->codigo}");
        });

        return back()->with('success', 'Parámetros guardados y horarios recalculados correctamente.');
    }

    // =========================================================
    // CU-18: cerrarGestion()
    // =========================================================
    public function cerrarGestion(Request $request)
    {
        $gestion = $this->getGestionActiva();
        if (!$gestion) return back()->with('error', 'No hay una gestión activa.');

        if (now()->toDateString() <= $gestion->fecha_fin) {
            return back()->with('error', 'No se puede cerrar la gestión antes de su fecha de fin (' . $gestion->fecha_fin . ').');
        }

        DB::transaction(function() use ($gestion) {
            $notaMinima = $gestion->nota_minima ?? 60;

            // Postulantes inscritos de esta gestión
            $inscritos = DB::table('postulante')
                ->where('gestion_grupo', $gestion->codigo)
                ->where('estado', 'inscrito')
                ->pluck('codigo');

            foreach ($inscritos as $codigo) {
                // Verificar si aprobó TODAS las materias (nota final >= nota_minima)
                $materiaReprobada = DB::table('examen')
                    ->where('codigo_postulante', $codigo)
                    ->select('id_materia', DB::raw('SUM(nota * ponderacion / 100.0) as nota_final'))
                    ->groupBy('id_materia')
                    ->havingRaw('SUM(nota * ponderacion / 100.0) < ?', [$notaMinima])
                    ->exists();

                DB::table('postulante')
                    ->where('codigo', $codigo)
                    ->update(['estado' => $materiaReprobada ? 'reprobado' : 'aprobado']);
            }

            // Asignar carreras a aprobados por promedio descendente
            $cupos = [];
            foreach (DB::table('carrera_gestion')->where('codigo_gestion', $gestion->codigo)->get() as $cg) {
                $key = $cg->codigo_carrera . '|' . $cg->plan_carrera . '|' . $cg->modalidad_carrera;
                $cupos[$key] = $cg->cupos;
            }

            $aprobados = DB::table('postulante as p')
                ->join('examen as e', 'p.codigo', '=', 'e.codigo_postulante')
                ->where('p.gestion_grupo', $gestion->codigo)
                ->where('p.estado', 'aprobado')
                ->select('p.codigo', DB::raw('AVG(e.nota * e.ponderacion / 100.0) as promedio'))
                ->groupBy('p.codigo')
                ->orderByDesc('promedio')
                ->get();

            $opciones = DB::table('postulante_carrera')
                ->whereIn('codigo_postulante', $aprobados->pluck('codigo'))
                ->whereIn('opcion', [1, 2])
                ->get()
                ->groupBy('codigo_postulante');

            foreach ($aprobados as $ap) {
                $asignada = false;
                foreach ([1, 2] as $opcion) {
                    $op = ($opciones[$ap->codigo] ?? collect())->firstWhere('opcion', $opcion);
                    if (!$op) continue;
                    $key = $op->codigo_carrera . '|' . $op->plan_carrera . '|' . $op->modalidad_carrera;
                    if (isset($cupos[$key]) && $cupos[$key] > 0) {
                        $cupos[$key]--;
                        DB::table('postulante_carrera')
                            ->where('codigo_postulante', $ap->codigo)
                            ->where('codigo_carrera', $op->codigo_carrera)
                            ->where('plan_carrera', $op->plan_carrera)
                            ->where('modalidad_carrera', $op->modalidad_carrera)
                            ->update(['asignada' => true]);
                        $asignada = true;
                        break;
                    }
                }
                if (!$asignada) {
                    $mejorKey = collect($cupos)->filter(fn($c) => $c > 0)->keys()->first();
                    if ($mejorKey) {
                        [$cod, $plan, $mod] = explode('|', $mejorKey);
                        $cupos[$mejorKey]--;
                        DB::table('postulante_carrera')->insert([
                            'codigo_postulante' => $ap->codigo,
                            'codigo_carrera'    => $cod,
                            'plan_carrera'      => $plan,
                            'modalidad_carrera' => $mod,
                            'opcion'            => null,
                            'asignada'          => true,
                        ]);
                    }
                }
            }

            $this->registrarBitacora("Cierre de gestión {$gestion->codigo} — estados y carreras asignadas");
        });

        return back()->with('success', 'Gestión cerrada. Estados actualizados y carreras asignadas.');
    }

    // =========================================================
    // CU-18: abrirGestion()
    // =========================================================
    public function abrirGestion(Request $request)
    {
        $gestionActiva = $this->getGestionActiva();
        if ($gestionActiva && now()->toDateString() <= $gestionActiva->fecha_fin) {
            return back()->with('error', 'No se puede abrir una nueva gestión mientras la actual esté vigente.');
        }

        $request->validate([
            'codigo'             => 'required|string|max:20|unique:gestion,codigo',
            'fecha_ini'          => 'required|date',
            'fecha_fin'          => 'required|date|after:fecha_ini',
            'fecha_inicio_notas' => 'required|date|after_or_equal:fecha_ini|before_or_equal:fecha_fin',
            'fecha_fin_notas'    => 'required|date|after:fecha_inicio_notas|before_or_equal:fecha_fin',
            'nota_minima'        => 'required|integer|min:0|max:100',
        ], [
            'codigo.unique'   => 'Ya existe una gestión con ese código.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la de inicio.',
        ]);

        $gestionActiva = $this->getGestionActiva();
        if ($gestionActiva && $request->fecha_ini <= $gestionActiva->fecha_fin) {
            return back()->withErrors(['fecha_ini' => 'La fecha de inicio debe ser posterior al fin de la gestión anterior (' . $gestionActiva->fecha_fin . ').'])->withInput();
        }

        DB::transaction(function() use ($request) {
            DB::table('gestion')->insert([
                'codigo'             => $request->codigo,
                'fecha_ini'          => $request->fecha_ini,
                'fecha_fin'          => $request->fecha_fin,
                'fecha_inicio_notas' => $request->fecha_inicio_notas,
                'fecha_fin_notas'    => $request->fecha_fin_notas,
                'nota_minima'        => $request->nota_minima,
            ]);
            $this->registrarBitacora("Apertura de nueva gestión: {$request->codigo}");
        });

        return back()->with('success', "Gestión {$request->codigo} abierta correctamente.");
    }
}