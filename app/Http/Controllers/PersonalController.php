<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\DatosPersonales;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersonalController extends Controller
{
    /**
     * 1. listarDocentes() [MODIFICADO PARA HOJAS BLADE]
     * Obtiene el personal docente uniendo sus datos civiles y aplicando filtros dinámicos.
     */
    public function listarDocentes(Request $request)
    {
        // Capturar los filtros desde la URL (Toolbar)
        $filtro = $request->input('filtro');
        $estado = $request->input('estado');

        $query = Personal::with('datosPersonales');

        // Aplicar buscador predictivo (CI, Registro, Nombre o Apellido)
        if (!empty($filtro)) {
            $query->where(function($q) use ($filtro) {
                $q->where('registro', 'LIKE', "%{$filtro}%")
                  ->orWhereHas('datosPersonales', function($subQ) use ($filtro) {
                      $subQ->where('nombre', 'LIKE', "%{$filtro}%")
                           ->orWhere('apellido', 'LIKE', "%{$filtro}%")
                           ->orWhere('ci', 'LIKE', "%{$filtro}%");
                  });
            });
        }

        // Aplicar selector por Estado Administrativo
        if (!empty($estado) && $estado !== 'Todos los estados') {
            $query->where('estado', filter_var($estado, FILTER_VALIDATE_BOOLEAN));
        }

        // Conteo total basándose en los filtros aplicados
        $totalDocentes = $query->count();

        // Paginación nativa adaptada al diseño (5 por página)
        $docentes = $query->paginate(5)->withQueryString();

        // Retornar la vista Blade inyectando todas las variables calculadas
        return view('docentes.index', compact('docentes', 'totalDocentes', 'filtro', 'estado'));
    }

    /**
     * 2. guardarDocente() [MODIFICADO EL RETORNO]
     * Registra en cadena los DatosPersonales, Personal y escribe en Bitácora.
     */
    public function guardarDocente(Request $request)
    {
        $validated = $request->validate([
            'ci'        => 'required|string|max:20|unique:datos_personales,ci',
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'genero'    => 'nullable|string|max:10',
            'telefono'  => 'nullable|string|max:20',
            'correo'    => 'nullable|email|max:150',
            'fecha_nac' => 'nullable|date',
            'direccion' => 'nullable|string|max:200',
            'registro'  => 'required|string|max:20|unique:personal,registro',
'estado'    => 'required'
        ]);

        DB::beginTransaction();
        try {
            $datos = DatosPersonales::create([
                'ci'        => $validated['ci'],
                'nombre'    => $validated['nombre'],
                'apellido'  => $validated['apellido'],
                'genero'    => $validated['genero'] ?? null,
                'telefono'  => $validated['telefono'] ?? null,
                'correo'    => $validated['correo'] ?? null,
                'fecha_nac' => $validated['fecha_nac'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
            ]);

            $docente = Personal::create([
                'registro' => $validated['registro'],
                'ci'       => $datos->ci,
                'estado'   => filter_var($validated['estado'], FILTER_VALIDATE_BOOLEAN),
            ]);

            $user = Auth::user();
            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "Registro de Docente. Administrador: {$user->user_name} creó al docente Registro: {$validated['registro']}.",
                'fecha_hora' => now(),
                'id_usuario' => $user->id
            ]);

            DB::commit();
            
            return redirect()->route('docentes.index')->with('success', 'Docente registrado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Ocurrió un error. Verifique los datos e intente nuevamente.']);
        }
    }

    /**
     * 3. actualizarDocente() [MODIFICADO EL RETORNO]
     */
    public function actualizarDocente(Request $request, $registro)
    {
        $docente = Personal::where('registro', $registro)->firstOrFail();

        $validated = $request->validate([
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'telefono'  => 'nullable|string|max:20',
            'correo'    => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:200',
            'estado'    => 'required'
        ]);

        DB::beginTransaction();
        try {
            $docente->datosPersonales()->update([
                'nombre'    => $validated['nombre'],
                'apellido'  => $validated['apellido'],
                'telefono'  => $validated['telefono'] ?? null,
                'correo'    => $validated['correo'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
            ]);

            $docente->update([
                'estado' => filter_var($validated['estado'], FILTER_VALIDATE_BOOLEAN)
            ]);

            $user = Auth::user();
            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "Modificación de Docente. Administrador: {$user->user_name} editó al docente Registro: {$registro}.",
                'fecha_hora' => now(),
                'id_usuario' => $user->id
            ]);

            DB::commit();
            return redirect()->route('docentes.index')->with('success', 'Datos del docente modificados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error. Verifique los datos e intente nuevamente.']);
        }
    }

    /**
     * 4. desactivarDocente() [MODIFICADO EL RETORNO]
     * Cambia el estado del docente a 'Inactivo' de manera lógica (Baja administrativa)
     */
    public function desactivarDocente(Request $request, $registro)
    {
        try {
            $docente = Personal::where('registro', $registro)->firstOrFail();
            
            $docente->update(['estado' => false]);

            $user = Auth::user();
            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "Desactivación de Docente. Administrador: {$user->user_name} dio de baja al docente Registro: {$registro}.",
                'fecha_hora' => now(),
                'id_usuario' => $user->id
            ]);

            return redirect()->route('docentes.index')->with('success', 'El docente ha sido inactivado en el sistema.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('docentes.index')->withErrors(['error' => 'Ocurrió un error. Verifique los datos e intente nuevamente.']);
        }
    }
}