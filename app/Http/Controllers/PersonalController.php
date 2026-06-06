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

        $perfil = $request->input('perfil', 'Todos los perfiles');

        $query = Personal::with('datosPersonales')
            ->join('usuario', 'personal.registro', '=', 'usuario.registro_personal')
            ->join('perfil', 'usuario.id_perfil', '=', 'perfil.id')
            ->select('personal.*', 'perfil.nombre as perfil_nombre');

        if ($perfil !== 'Todos los perfiles') {
            $query->where('perfil.id', $perfil);
        }

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

        $perfiles = DB::table('perfil')->get();
    
        // Retornar la vista Blade inyectando todas las variables calculadas
        return view('personal.index', compact('docentes', 'totalDocentes', 'filtro', 'estado', 'perfil', 'perfiles'));
    }

    /**
     * 2. guardarDocente() [MODIFICADO EL RETORNO]
     * Registra en cadena los DatosPersonales, Personal y escribe en Bitácora.
     */
    public function guardarDocente(Request $request)
    {
        $validated = $request->validate([
            'ci'       => 'required|string|max:20|unique:datos_personales,ci',
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'genero'   => 'required|in:m,f',
            'fecha_nac'=> 'nullable|date',
            'telefono' => 'nullable|string|max:20',
            'correo'   => 'required|email|max:150|unique:datos_personales,correo',
            'direccion'=> 'nullable|string|max:200',
            'registro' => 'required|string|max:20|unique:personal,registro',
            'id_perfil'=> 'required|exists:perfil,id',
        ]);

        DB::beginTransaction();
        try {
            // Datos personales
            DatosPersonales::create([
                'ci'        => $validated['ci'],
                'nombre'    => $validated['nombre'],
                'apellido'  => $validated['apellido'],
                'genero'    => $validated['genero'],
                'fecha_nac' => $validated['fecha_nac'] ?? null,
                'telefono'  => $validated['telefono'] ?? null,
                'correo'    => $validated['correo'],
                'direccion' => $validated['direccion'] ?? null,
            ]);

            // Personal
            Personal::create([
                'registro' => $validated['registro'],
                'ci'       => $validated['ci'],
                'estado'   => true,
            ]);

            // Credenciales si es docente
            $perfilNombre = DB::table('perfil')->where('id', $validated['id_perfil'])->value('nombre');
            if (strtolower($perfilNombre) === 'docente' && $request->has('credenciales')) {
                foreach ($request->credenciales as $cred) {
                    RequisitosPersonal::create([
                        'registro_personal' => $validated['registro'],
                        'area'       => $cred['area'] ?? null,
                        'nivel_grado'=> $cred['nivel_grado'] ?? null,
                        'nivel_exp'  => $cred['nivel_exp'] ?? null,
                        'maestria'   => $cred['maestria'],
                        'doctorado'  => $cred['doctorado'],
                        'diplomado'  => $cred['diplomado'],
                    ]);
                }
            }

            // Generar usuario automático
            $inicial = strtolower(substr($validated['nombre'], 0, 1));
            $apellidoLimpio = strtolower(preg_replace('/\s+/', '', $validated['apellido']));
            $numero = rand(10, 99);
            $userName = $inicial . $apellidoLimpio . $numero;

            // Verificar que no exista
            while (DB::table('usuario')->where('user_name', $userName)->exists()) {
                $numero = rand(10, 99);
                $userName = $inicial . $apellidoLimpio . $numero;
            }

            DB::table('usuario')->insert([
                'user_name'         => $userName,
                'clave'             => bcrypt($validated['ci']),
                'email'             => $validated['correo'],
                'id_perfil'         => $validated['id_perfil'],
                'registro_personal' => $validated['registro'],
            ]);

            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "Registro de Docente. Administrador: " . Auth::user()->user_name . " registró al personal: {$validated['registro']}.",
                'fecha_hora' => now(),
                'id_usuario' => Auth::id()
            ]);

            DB::commit();
            return redirect()->route('personal.index')
                ->with('success', "Personal registrado. Usuario: {$userName} · Contraseña inicial: CI del personal.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Error al registrar. Verifique los datos.']);
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
            return redirect()->route('personal.show', $registro)
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

            return redirect()->route('personal.index')->with('success', 'El docente ha sido inactivado en el sistema.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('personal.index')->withErrors(['error' => 'Ocurrió un error. Verifique los datos e intente nuevamente.']);
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

            return redirect()->route('personal.index')->with('success', 'El docente ha sido activado correctamente.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('personal.index')->withErrors(['error' => 'Ocurrió un error.']);
        }
    }

    public function verDocente($registro)
    {
        $docente = Personal::with(['datosPersonales', 'requisitosPersonal'])
            ->where('registro', $registro)->firstOrFail();

        return view('personal.show', compact('docente'));
    }

    public function editarDocente($registro)
    {
        $docente = Personal::with(['datosPersonales', 'requisitosPersonal'])
            ->where('registro', $registro)->firstOrFail();

        return view('personal.edit', compact('docente'));
    }

    public function crearDocente()
    {
        $perfiles = DB::table('perfil')->get();
        return view('personal.create', compact('perfiles'));
    }
}