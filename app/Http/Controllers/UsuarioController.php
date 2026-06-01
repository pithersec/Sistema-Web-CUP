<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Bitacora;
use App\Models\Personal; // Añadido para la vinculación en listas/formularios
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * CU-01: Iniciar Sesión - enviarCredenciales()
     */
    public function enviarCredenciales(Request $request)
    {
        $credentials = $request->validate([
            'usuario' => 'required|string', 
            'clave'   => 'required|string', 
        ]);

        $user = Usuario::where('user_name', $credentials['usuario'])->first();

        if (!$user || !Hash::check($credentials['clave'], $user->clave)) {
            return redirect()->back()->withInput()->withErrors(['login_error' => 'Credenciales incorrectas. Verifique su usuario y contraseña.']);
        }

        Auth::login($user);
        $perfil = $user->perfil; 

        Bitacora::create([
            'ip'         => $request->ip(),
            'accion'     => "Inicio de sesión exitoso. Usuario: {$user->user_name} con Perfil: {$perfil->nombre}.",
            'fecha_hora' => now(),
            'id_usuario' => $user->id
        ]);

        return redirect()->intended('/dashboard');
    }

    /**
     * CU-02: Cerrar Sesión - cerrarSesión()
     */
    public function cerrarSesión(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "Cierre de sesión exitoso. Usuario: {$user->user_name}.",
                'fecha_hora' => now(),
                'id_usuario' => $user->id
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/login');
    }

    /**
     * CU-16: listarUsuarios() [ADAPTADO PARA BLADE VISTAS]
     * Lista los usuarios del sistema cargando su respectivo Perfil y Personal asignado.
     */
    public function listarUsuarios(Request $request)
    {
        $filtro = $request->input('filtro');
        $perfil_id = $request->input('id_perfil');

        // Construir la consulta con relaciones anidadas para obtener los nombres reales del personal
        $query = Usuario::with(['perfil', 'personal.datosPersonales']);

        // Filtro de búsqueda predictiva
        if (!empty($filtro)) {
            $query->where(function($q) use ($filtro) {
                $q->where('user_name', 'LIKE', "%{$filtro}%")
                  ->orWhere('email', 'LIKE', "%{$filtro}%")
                  ->orWhereHas('perfil', function($subQ) use ($filtro) {
                      $subQ->where('nombre', 'LIKE', "%{$filtro}%");
                  });
            });
        }

        // Filtro por Rol / Perfil desde la barra de herramientas
        if (!empty($perfil_id) && $perfil_id !== 'Todos los perfiles') {
            $query->where('id_perfil', $perfil_id);
        }

        // Paginación estructural nativa para la vista
        $usuarios = $query->paginate(5)->withQueryString();
        $totalUsuarios = $query->count();

        // Obtener listado de perfiles existentes para renderizar el selector dinámico
        $perfiles = DB::table('perfil')->get();

        return view('admin.usuarios', compact('usuarios', 'totalUsuarios', 'perfiles', 'filtro', 'perfil_id'));
    }

    /**
     * 2. guardarUsuario() [ADAPTADO PARA WEB]
     */
    public function guardarUsuario(Request $request)
    {
        $validated = $request->validate([
            'user_name'         => 'required|string|max:255|unique:usuario,user_name',
            'email'             => 'required|string|email|max:255|unique:usuario,email',
            'clave'             => 'required|string|min:6', 
            'id_perfil'         => 'required|exists:perfil,id',
            'registro_personal' => 'nullable|exists:personal,registro'
        ]);

        try {
            Usuario::create([
                'user_name'         => $validated['user_name'],
                'email'             => $validated['email'],
                'clave'             => Hash::make($validated['clave']), 
                'id_perfil'         => $validated['id_perfil'],
                'registro_personal' => $validated['registro_personal']
            ]);

            $admin = Auth::user();
            Bitacora::create([
                'ip'         => $request->ip(),
                'accion'     => "Creación de Usuario. Administrador: {$admin->user_name} creó la cuenta: {$validated['user_name']}.",
                'fecha_hora' => now(),
                'id_usuario' => $admin->id
            ]);

            return redirect('/admin/usuarios')->with('success', 'Usuario registrado con éxito en la plataforma.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => 'No se pudo crear el usuario: ' . $e->getMessage()]);
        }
    }

    /**
     * 3. actualizarUsuario() [ADAPTADO PARA WEB]
     */
    public function actualizarUsuario(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $validated = $request->validate([
            'user_name' => 'required|string|max:255|unique:usuario,user_name,' . $id,
            'email'     => 'required|string|email|max:255|unique:usuario,email,' . $id,
            'clave'     => 'nullable|string|min:6', 
            'id_perfil' => 'required|exists:perfil,id'
        ]);

        $updateData = [
            'user_name' => $validated['user_name'],
            'email'     => $validated['email'],
            'id_perfil' => $validated['id_perfil']
        ];

        if (!empty($validated['clave'])) {
            $updateData['clave'] = Hash::make($validated['clave']);
        }

        $usuario->update($updateData);

        $admin = Auth::user();
        Bitacora::create([
            'ip'         => $request->ip(),
            'accion'     => "Modificación de Usuario. Administrador: {$admin->user_name} actualizó la cuenta ID: {$id}.",
            'fecha_hora' => now(),
            'id_usuario' => $admin->id
        ]);

        return redirect('/admin/usuarios')->with('success', 'Los datos del usuario han sido actualizados.');
    }

    /**
     * 4. eliminarUsuario() [ADAPTADO PARA WEB]
     */
    public function eliminarUsuario(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        $nombreAfectado = $usuario->user_name;
        
        $usuario->delete();

        $admin = Auth::user();
        Bitacora::create([
            'ip'         => $request->ip(),
            'accion'     => "Eliminación de Usuario. Administrador: {$admin->user_name} eliminó la cuenta de: {$nombreAfectado}.",
            'fecha_hora' => now(),
            'id_usuario' => $admin->id
        ]);

        return redirect('/admin/usuarios')->with('success', "La cuenta de {$nombreAfectado} fue removida físicamente.");
    }
}