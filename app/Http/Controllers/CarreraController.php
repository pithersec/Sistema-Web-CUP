<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Gestion;
use App\Models\Bitacora;
use App\Models\Postulante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarreraController extends Controller
{
    /**
     * 1. listarCarreras()
     * Muestra la matriz de carreras, sus cupos asignados por gestión y calcula la ocupación real.
     */
    public function listarCarreras(Request $request)
    {
        $gestiones = Gestion::orderBy('codigo', 'desc')->get();

        $gestion_seleccionada = $request->input('codigo_gestion');
        if (empty($gestion_seleccionada) && $gestiones->isNotEmpty()) {
            $gestion_seleccionada = $gestiones->first()->codigo;
        }

        $carreras = DB::table('carrera')
            ->leftJoin('carrera_gestion', function($join) use ($gestion_seleccionada) {
                $join->on('carrera.codigo', '=', 'carrera_gestion.codigo_carrera')
                    ->on('carrera.plan', '=', 'carrera_gestion.plan_carrera')
                    ->on('carrera.modalidad', '=', 'carrera_gestion.modalidad_carrera')
                    ->where('carrera_gestion.codigo_gestion', '=', $gestion_seleccionada);
            })
            ->select(
                'carrera.codigo',
                'carrera.plan',
                'carrera.nombre',
                'carrera.modalidad',
                'carrera_gestion.cupos'
            )->get();

        foreach ($carreras as $carrera) {
            $carrera->ocupados = DB::table('postulante_carrera')
                ->join('postulante', 'postulante_carrera.codigo_postulante', '=', 'postulante.codigo')
                ->join('grupo', 'postulante.id_grupo', '=', 'grupo.id')
                ->where('postulante_carrera.codigo_carrera', $carrera->codigo)
                ->where('postulante_carrera.plan_carrera', $carrera->plan)
                ->where('postulante_carrera.modalidad_carrera', $carrera->modalidad)
                ->where('grupo.codigo_gestion', $gestion_seleccionada)
                ->count();

            $carrera->cupos = $carrera->cupos ?? 0;
        }

        return view('carreras.index', compact('gestiones', 'gestion_seleccionada', 'carreras'));
    }

    /**
     * 2. guardarMasivo()
     */
    public function guardarMasivo(Request $request)
    {
        $codigo_gestion = $request->input('codigo_gestion');
        $cupos_input = $request->input('cupos');

        if (empty($cupos_input)) {
            return redirect()->back()->with('error', 'No hay datos de cupos para procesar.');
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();

            // cupos_input viene como [codigo|plan|modalidad => cupos]
            foreach ($cupos_input as $key => $cupos) {
                [$codigo_carrera, $plan_carrera, $modalidad_carrera] = explode('|', $key);
                $cupos = max(0, intval($cupos));

                $existe = DB::table('carrera_gestion')
                    ->where('codigo_carrera', $codigo_carrera)
                    ->where('plan_carrera', $plan_carrera)
                    ->where('modalidad_carrera', $modalidad_carrera)
                    ->where('codigo_gestion', $codigo_gestion)
                    ->exists();

                if ($existe) {
                    DB::table('carrera_gestion')
                        ->where('codigo_carrera', $codigo_carrera)
                        ->where('plan_carrera', $plan_carrera)
                        ->where('modalidad_carrera', $modalidad_carrera)
                        ->where('codigo_gestion', $codigo_gestion)
                        ->update(['cupos' => $cupos]);
                } else {
                    if ($cupos > 0) {
                        DB::table('carrera_gestion')->insert([
                            'codigo_carrera'    => $codigo_carrera,
                            'plan_carrera'      => $plan_carrera,
                            'modalidad_carrera' => $modalidad_carrera,
                            'codigo_gestion'    => $codigo_gestion,
                            'cupos'             => $cupos,
                        ]);
                    }
                }
            }

            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "Actualización Masiva de Cupos. Administrador: {$user->user_name} actualizó los cupos para la Gestión: {$codigo_gestion}.",
                'fecha_hora' => now(),
                'id_usuario' => $user->id
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Todos los cupos se actualizaron correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar: ' . $e->getMessage());
        }
    }

    /**
     * 3. guardarCuposFila()
     */
    public function guardarCuposFila(Request $request)
    {
        $validated = $request->validate([
            'codigo_carrera'    => 'required|string',
            'plan_carrera'      => 'required|string',
            'modalidad_carrera' => 'required|string',
            'codigo_gestion'    => 'required|string',
            'cupos'             => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $existe = DB::table('carrera_gestion')
                ->where('codigo_carrera', $validated['codigo_carrera'])
                ->where('plan_carrera', $validated['plan_carrera'])
                ->where('modalidad_carrera', $validated['modalidad_carrera'])
                ->where('codigo_gestion', $validated['codigo_gestion'])
                ->exists();

            if ($existe) {
                DB::table('carrera_gestion')
                    ->where('codigo_carrera', $validated['codigo_carrera'])
                    ->where('plan_carrera', $validated['plan_carrera'])
                    ->where('modalidad_carrera', $validated['modalidad_carrera'])
                    ->where('codigo_gestion', $validated['codigo_gestion'])
                    ->update(['cupos' => $validated['cupos']]);
                $accion = "Modificación de Cupos";
            } else {
                DB::table('carrera_gestion')->insert([
                    'codigo_carrera'    => $validated['codigo_carrera'],
                    'plan_carrera'      => $validated['plan_carrera'],
                    'modalidad_carrera' => $validated['modalidad_carrera'],
                    'codigo_gestion'    => $validated['codigo_gestion'],
                    'cupos'             => $validated['cupos'],
                ]);
                $accion = "Asignación de Cupos";
            }

            $user = Auth::user();
            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "{$accion}. Administrador: {$user->user_name} fijó {$validated['cupos']} cupos para {$validated['codigo_carrera']}-{$validated['plan_carrera']} ({$validated['modalidad_carrera']}).",
                'fecha_hora' => now(),
                'id_usuario' => $user->id
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Cupos guardados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}