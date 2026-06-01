<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarreraController extends Controller
{
    /**
     * 1. listarCarreras() [ADAPTADO PARA WEB]
     * Muestra la matriz de carreras, sus cupos asignados por gestión y calcula la ocupación real.
     */
    public function listarCarreras(Request $request)
    {
        // 1. Obtener todas las gestiones para el selector superior
        $gestiones = DB::table('gestion')->orderBy('codigo', 'desc')->get();

        // 2. Determinar la gestión seleccionada (por defecto la primera que encuentre)
        $gestion_seleccionada = $request->input('codigo_gestion');
        if (empty($gestion_seleccionada) && $gestiones->isNotEmpty()) {
            $gestion_seleccionada = $gestiones->first()->codigo;
        }

        // 3. Obtener las carreras y cruzar con la tabla intermedia 'carrera_gestion'
        // Además, calculamos cuántos postulantes ya están inscritos/admitidos en esa carrera para esa gestión
        $carreras = DB::table('carrera')
            ->leftJoin('carrera_gestion', function($join) use ($gestion_seleccionada) {
                $join->on('carrera.codigo', '=', 'carrera_gestion.codigo_carrera')
                     ->where('carrera_gestion.codigo_gestion', '=', $gestion_seleccionada);
            })
            ->select(
                'carrera.codigo',
                'carrera.nombre',
                'carrera.modalidad',
                'carrera_gestion.id as carrera_gestion_id',
                'carrera_gestion.cupos'
            )->get();

        // 4. Calcular dinámicamente los inscritos por carrera para armar las barras de progreso
        foreach ($carreras as $carrera) {
            // Contamos postulantes asociados a este código de carrera y gestión
            // Asumiendo que tu tabla postulantes tiene 'codigo_carrera' y 'codigo_gestion'
            $carrera->ocupados = DB::table('postulante')
                ->where('codigo_carrera', $carrera->codigo)
                ->where('codigo_gestion', $gestion_seleccionada)
                ->count();
                
            // Si no tiene cupos asignados en la intermedia, por defecto es 0
            $carrera->cupos = $carrera->cupos ?? 0;
        }

        return view('carreras.index', compact('gestiones', 'gestion_seleccionada', 'carreras'));
    }

    /**
     * 2. guardarMasivo() [NUEVO MÉTODO PARA EL BOTÓN PRINCIPAL DE LA VISTA]
     * Procesa todas las filas de cupos enviadas en el formulario simultáneamente.
     */
    public function guardarMasivo(Request $request)
    {
        $codigo_gestion = $request->input('codigo_gestion');
        $cupos_input = $request->input('cupos'); // Array asociativo [codigo_carrera => cantidad_cupos]

        if (empty($cupos_input)) {
            return redirect()->back()->with('error', 'No hay datos de cupos para procesar.');
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();

            foreach ($cupos_input as $codigo_carrera => $cupos) {
                $cupos = max(0, intval($cupos)); // Validar que sea un entero positivo o cero

                // Verificar si ya existe la relación en la tabla intermedia
                $existe = DB::table('carrera_gestion')
                    ->where('codigo_carrera', $codigo_carrera)
                    ->where('codigo_gestion', $codigo_gestion)
                    ->first();

                if ($existe) {
                    // Actualizar cupos existentes
                    DB::table('carrera_gestion')
                        ->where('id', $existe->id)
                        ->update(['cupos' => $cupos]);
                } else {
                    // Insertar nueva asignación si es mayor a cero
                    if ($cupos > 0) {
                        DB::table('carrera_gestion')->insert([
                            'codigo_carrera' => $codigo_carrera,
                            'codigo_gestion' => $codigo_gestion,
                            'cupos'          => $cupos
                        ]);
                    }
                }
            }

            // Registrar en Bitácora el cambio global
            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "Actualización Masiva de Cupos. Administrador: {$user->user_name} actualizó los parámetros de oferta académica para la Gestión ID: {$codigo_gestion}.",
                'fecha_hora' => now(),
                'id_usuario' => $user->id
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Todos los cupos se actualizaron correctamente para el periodo académico.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar la actualización: ' . $e->getMessage());
        }
    }

    /**
     * 3. guardarCuposFila() [MÉTODO PARA CADA BOTÓN INDIVIDUAL DE FILA]
     */
    public function guardarCuposFila(Request $request)
    {
        $validated = $request->validate([
            'codigo_carrera' => 'required|string',
            'codigo_gestion' => 'required|integer',
            'cupos'          => 'required|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            $existe = DB::table('carrera_gestion')
                ->where('codigo_carrera', $validated['codigo_carrera'])
                ->where('codigo_gestion', $validated['codigo_gestion'])
                ->first();

            if ($existe) {
                DB::table('carrera_gestion')
                    ->where('id', $existe->id)
                    ->update(['cupos' => $validated['cupos']]);
                $accion = "Modificación de Cupos";
            } else {
                DB::table('carrera_gestion')->insert([
                    'codigo_carrera' => $validated['codigo_carrera'],
                    'codigo_gestion' => $validated['codigo_gestion'],
                    'cupos'          => $validated['cupos']
                ]);
                $accion = "Asignación de Cupos";
            }

            $user = Auth::user();
            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "{$accion}. Administrador: {$user->user_name} fijó {$validated['cupos']} cupos para la carrera {$validated['codigo_carrera']}.",
                'fecha_hora' => now(),
                'id_usuario' => $user->id
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Cupos guardados para la carrera seleccionada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al guardar la fila: ' . $e->getMessage());
        }
    }
}
