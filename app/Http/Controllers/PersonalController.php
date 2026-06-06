<?php

namespace App\Http\Controllers;

use App\Models\Personal;
use App\Models\DatosPersonales;
use App\Models\Bitacora;
use App\Models\RequisitosPersonal;
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
                            ->orWhere('ci', 'LIKE', "%{$filtro}%")
                            ->orWhere('correo', 'LIKE', "%{$filtro}%")
                            ->orWhere('telefono', 'LIKE', "%{$filtro}%");
                    });
            });
        }

        // Aplicar selector por Estado Administrativo
        if ($estado !== null && $estado !== 'Todos los estados') {
            $query->where('estado', $estado == '1' ? true : false);
        }

        // Conteo total basándose en los filtros aplicados
        $totalDocentes = $query->count();

        // Paginación nativa adaptada al diseño (5 por página)
        $docentes = $query->paginate(15)->withQueryString();

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
        $docente = Personal::with(['datosPersonales', 'requisitosPersonal'])
            ->where('registro', $registro)->firstOrFail();

        $validated = $request->validate([
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'telefono'  => 'nullable|string|max:20',
            'correo'    => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:200',
            'estado'    => 'required',
            'credenciales.*.maestria'  => 'required|string|max:50',
            'credenciales.*.doctorado' => 'required|string|max:50',
            'credenciales.*.diplomado' => 'required|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            $docente->datosPersonales()->update([
                'nombre'    => $validated['nombre'],
                'apellido'  => $validated['apellido'],
                'telefono'  => $request->telefono,
                'correo'    => $request->correo,
                'direccion' => $request->direccion,
                'fecha_nac' => $request->fecha_nac,
            ]);

            // Manejar credenciales
            $idsExistentes = $docente->requisitosPersonal->pluck('id')->toArray();
            $idsEnviados = [];

            foreach ($request->credenciales ?? [] as $cred) {
                if (!empty($cred['id'])) {
                    // Actualizar existente
                    RequisitosPersonal::where('id', $cred['id'])->update([
                        'area'       => $cred['area'] ?? null,
                        'nivel_grado'=> $cred['nivel_grado'] ?? null,
                        'nivel_exp'  => $cred['nivel_exp'] ?? null,
                        'maestria'   => $cred['maestria'],
                        'doctorado'  => $cred['doctorado'],
                        'diplomado'  => $cred['diplomado'],
                    ]);
                    $idsEnviados[] = $cred['id'];
                } else {
                    // Nueva credencial
                    RequisitosPersonal::create([
                        'registro_personal' => $registro,
                        'area'       => $cred['area'] ?? null,
                        'nivel_grado'=> $cred['nivel_grado'] ?? null,
                        'nivel_exp'  => $cred['nivel_exp'] ?? null,
                        'maestria'   => $cred['maestria'],
                        'doctorado'  => $cred['doctorado'],
                        'diplomado'  => $cred['diplomado'],
                    ]);
                }
            }

            // Eliminar las que se borraron con el botón ✕
            $aEliminar = array_diff($idsExistentes, $idsEnviados);
            if (!empty($aEliminar)) {
                RequisitosPersonal::whereIn('id', $aEliminar)->delete();
            }

            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "Modificación de Docente. Administrador: " . Auth::user()->user_name . " editó al docente Registro: {$registro}.",
                'fecha_hora' => now(),
                'id_usuario' => Auth::id()
            ]);

            DB::commit();
            return redirect()->route('docentes.show', $registro)
                ->with('success', 'Datos del docente actualizados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error. Verifique los datos.']);
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

    public function activarDocente(Request $request, $registro)
    {
        try {
            $docente = Personal::where('registro', $registro)->firstOrFail();
            $docente->update(['estado' => true]);

            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "Activación de Docente. Administrador: " . Auth::user()->user_name . " activó al docente Registro: {$registro}.",
                'fecha_hora' => now(),
                'id_usuario' => Auth::id()
            ]);

            return redirect()->route('docentes.index')->with('success', 'El docente ha sido activado correctamente.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('docentes.index')->withErrors(['error' => 'Ocurrió un error.']);
        }
    }

    public function verDocente($registro)
    {
        $docente = Personal::with(['datosPersonales', 'requisitosPersonal'])
            ->where('registro', $registro)->firstOrFail();

        return view('docentes.show', compact('docente'));
    }

    public function editarDocente($registro)
    {
        $docente = Personal::with(['datosPersonales', 'requisitosPersonal'])
            ->where('registro', $registro)->firstOrFail();

        return view('docentes.edit', compact('docente'));
    }
}