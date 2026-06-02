<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\CarreraGestion;
use App\Models\Gestion;
use App\Models\Bitacora;
use App\Models\Postulante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CarreraController extends Controller
{
    /**
     * 1. listarCarreras()
     * Muestra la matriz de carreras, sus cupos asignados por gestión y calcula la ocupación real.
     */
    public function listarCarreras(Request $request)
    {
        // 1. Obtener todas las gestiones para el selector superior usando el Modelo
        $gestiones = Gestion::orderBy('codigo', 'desc')->get();

        // 2. Determinar la gestión seleccionada (por defecto la primera)
        $gestion_seleccionada = $request->input('codigo_gestion');
        if (empty($gestion_seleccionada) && $gestiones->isNotEmpty()) {
            $gestion_seleccionada = $gestiones->first()->codigo;
        }

        // 3. Obtener las carreras y cruzar con 'carrera_gestion' usando la columna física 'codigo_gestion'
        $carreras = DB::table('carrera')
            ->leftJoin('carrera_gestion', function($join) use ($gestion_seleccionada) {
                $join->on('carrera.codigo', '=', 'carrera_gestion.codigo_carrera')
                     ->where('carrera_gestion.codigo_gestion', '=', $gestion_seleccionada);
            })
            ->select(
                'carrera.codigo',
                'carrera.nombre',
                'carrera.modalidad',
                'carrera_gestion.cupos'
            )->get();

        // 4. Calcular los inscritos cruzando con la tabla 'grupo' para validar la gestión correcta
        foreach ($carreras as $carrera) {
            $carrera->ocupados = DB::table('postulante')
                ->join('grupo', 'postulante.id_grupo', '=', 'grupo.id') // Unimos con grupo para llegar a la gestión
                ->where('postulante.codigo_carrera1', $carrera->codigo)
                ->where('grupo.codigo_gestion', $gestion_seleccionada)  // Filtramos por la gestión del grupo
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
        $request->validate([
            'codigo_gestion' => 'required|string|exists:gestion,codigo',
            'cupos'           => 'required|array',
            'cupos.*'         => 'integer|min:0',
        ]);

        if (!Auth::user()) {
            return redirect()->route('login');
        }

        $codigo_gestion = $request->input('codigo_gestion'); 
        $cupos_input = $request->input('cupos'); 

        if (empty($cupos_input)) {
            return redirect()->back()->with('error', 'No hay datos de cupos para procesar.');
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();

            foreach ($cupos_input as $codigo_carrera => $cupos) {
                $cupos = max(0, intval($cupos)); 

                $existe = DB::table('carrera_gestion')
                    ->where('codigo_carrera', $codigo_carrera)
                    ->where('codigo_gestion', $codigo_gestion)
                    ->exists();

                if ($existe) {
                    DB::table('carrera_gestion')
                        ->where('codigo_carrera', $codigo_carrera)
                        ->where('codigo_gestion', $codigo_gestion)
                        ->update(['cupos' => $cupos]);
                } else {
                    if ($cupos > 0) {
                        DB::table('carrera_gestion')->insert([
                            'codigo_carrera' => $codigo_carrera,
                            'codigo_gestion' => $codigo_gestion,
                            'cupos'          => $cupos
                        ]);
                    }
                }
            }

            // Registro en Bitácora con los campos exactos de tu modelo
            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "Actualización Masiva de Cupos. Administrador: {$user->user_name} actualizó los parámetros de oferta académica para la Gestión: {$codigo_gestion}.",
                'fecha_hora' => now(),
                'id_usuario' => $user->id
            ]);

            DB::commit();
            return redirect()->route('carreras.index')->with('success', 'Todos los cupos se actualizaron correctamente para el periodo académico.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('carreras.index')->with('error', 'Ocurrió un error. Verifique los datos e intente nuevamente.');
        }
    }

    /**
     * 3. guardarCuposFila()
     */
    public function guardarCuposFila(Request $request)
    {
        $validated = $request->validate([
            'codigo_carrera' => 'required|string',
            'codigo_gestion' => 'required|string', 
            'cupos'          => 'required|integer|min:0'
        ]);

        if (!Auth::user()) {
            return redirect()->route('login');
        }

        DB::beginTransaction();
        try {
            $existe = DB::table('carrera_gestion')
                ->where('codigo_carrera', $validated['codigo_carrera'])
                ->where('codigo_gestion', $validated['codigo_gestion'])
                ->exists();

            if ($existe) {
                DB::table('carrera_gestion')
                    ->where('codigo_carrera', $validated['codigo_carrera'])
                    ->where('codigo_gestion', $validated['codigo_gestion'])
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
            return redirect()->route('carreras.index')->with('success', 'Cupos guardados para la carrera seleccionada.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('carreras.index')->with('error', 'Ocurrió un error. Verifique los datos e intente nuevamente.');
        }
    }

    public function guardarCupos(Request $request)
    {
        return $this->guardarCuposFila($request);
    }

    public function actualizarCupos(Request $request, $id_carrera_gestion)
    {
        $carreraGestion = CarreraGestion::findOrFail($id_carrera_gestion);

        $validated = $request->validate([
            'cupos' => 'required|integer|min:0',
        ]);

        $carreraGestion->update(['cupos' => $validated['cupos']]);

        $user = Auth::user();
        Bitacora::create([
            'ip' => $request->ip(),
            'accion' => "Actualización de Cupos. Administrador: {$user->user_name} actualizó cupos del registro ID: {$id_carrera_gestion}.",
            'fecha_hora' => now(),
            'id_usuario' => $user->id
        ]);

        return redirect()->back()->with('success', 'Cupos actualizados correctamente.');
    }
}