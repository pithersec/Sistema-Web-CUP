<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asistencia;
use App\Models\GrupoMateria;
use App\Models\Postulante;
use Illuminate\Support\Facades\Auth;

class AsistenciaController extends Controller
{
    public function mostrarAsistencia(Request $request)
    {
        $usuario = Auth::user();
        $registro = $usuario->registro_personal;

        $gestiones = \App\Models\Gestion::orderByRaw("SPLIT_PART(codigo, '-', 2) DESC, SPLIT_PART(codigo, '-', 1) DESC")->get();
        $gestionCodigo = $request->input('gestion', $gestiones->first()?->codigo);

        $esAdmin = in_array($usuario->perfil->nombre, ['Administrador', 'Sistema']);

        if ($esAdmin) {
            $gruposMateria = GrupoMateria::with(['materia', 'grupo'])
                ->where('gestion_grupo', $gestionCodigo)
                ->get();
        } else {
            $gruposMateria = GrupoMateria::with(['materia', 'grupo'])
                ->where('registro_personal', $registro)
                ->where('gestion_grupo', $gestionCodigo)
                ->get();
        }

        $grupoSeleccionado  = $request->input('id_grupo');
        $gestionSeleccionada = $request->input('codigo_gestion');
        $materiaSeleccionada = $request->input('id_materia');
        $fecha = $request->input('fecha', now()->toDateString());

        $gestionObj = $gestiones->firstWhere('codigo', $gestionCodigo);
        if ($gestionObj) {
            $fechaMin = \Carbon\Carbon::parse($gestionObj->fecha_ini)->toDateString();
            if ($fecha < $fechaMin) $fecha = $fechaMin;
            if ($fecha > now()->toDateString()) $fecha = now()->toDateString();
        }

        $postulantes = collect();
        $asistencias = collect();

        if ($grupoSeleccionado && $gestionSeleccionada && $materiaSeleccionada) {
            $postulantes = Postulante::with('datosPersonales')
                ->where('id_grupo', $grupoSeleccionado)
                ->where('gestion_grupo', $gestionSeleccionada)
                ->get();

            $asistencias = Asistencia::where('codigo_gestion', $gestionSeleccionada)
                ->where('id_grupo', $grupoSeleccionado)
                ->where('id_materia', $materiaSeleccionada)
                ->where('fecha', $fecha)
                ->get();
        }

        return view('asistencia.index', compact(
            'gruposMateria', 'postulantes', 'asistencias',
            'grupoSeleccionado', 'gestionSeleccionada', 'materiaSeleccionada',
            'fecha', 'esAdmin', 'gestiones', 'gestionCodigo'
        ));
    }

    public function registrarAsistencia(Request $request)
    {
        $request->validate([
            'id_grupo'        => 'required',
            'codigo_gestion'  => 'required',
            'id_materia'      => 'required',
            'fecha'           => 'required|date',
            'asistencias'     => 'required|array',
        ]);

        $fecha           = $request->input('fecha');
        $idGrupo         = $request->input('id_grupo');
        $codigoGestion   = $request->input('codigo_gestion');
        $idMateria       = $request->input('id_materia');
        $asistenciasInput = $request->input('asistencias', []);

        // Validar rango de fecha
        $gestion = \App\Models\Gestion::where('codigo', $codigoGestion)->first();
        if ($gestion) {
            $fechaMin = \Carbon\Carbon::parse($gestion->fecha_ini)->toDateString();
            $fechaMax = now()->toDateString();
            if ($fecha < $fechaMin || $fecha > $fechaMax) {
                return back()->with('error', 'La fecha debe estar entre el inicio de la gestión y hoy.');
            }
        }

        // Verificar horario si es docente
        $usuario = Auth::user();
        $esAdmin = $usuario->perfil->nombre === 'Administrador';

        if (!$esAdmin) {
            $diaSemana = now()->dayOfWeek; // 0=domingo
            $grupoMateria = GrupoMateria::where('id_grupo', $idGrupo)
                ->where('gestion_grupo', $codigoGestion)
                ->where('id_materia', $idMateria)
                ->where('registro_personal', $usuario->registro_personal)
                ->first();

            if (!$grupoMateria) {
                return back()->with('error', 'No tienes permiso para registrar asistencia en este grupo.');
            }
        }

        // Obtener TODOS los postulantes del grupo para registrar también los ausentes
        $todosPostulantes = Postulante::where('id_grupo', $idGrupo)
            ->where('gestion_grupo', $codigoGestion)
            ->pluck('codigo');

        $data = [];
        foreach ($asistenciasInput as $codigoPostulante => $presente) {
            $data[] = [
                'fecha'             => $fecha,
                'codigo_postulante' => $codigoPostulante,
                'codigo_gestion'    => $codigoGestion,
                'id_grupo'          => $idGrupo,
                'id_materia'        => $idMateria,
                'presente'          => true,
            ];
        }

        if (!empty($data)) {
            Asistencia::upsert($data, ['fecha', 'codigo_postulante', 'codigo_gestion', 'id_grupo', 'id_materia'], ['presente']);
        }     

        return back()->with('success', 'Asistencia registrada correctamente.');
    }
}