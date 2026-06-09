<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PostulanteController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| 1. RUTAS PÚBLICAS (Invitados / Sin Autenticación)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.welcome');
})->name('welcome');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [UsuarioController::class, 'enviarCredenciales']);

Route::get('/preinscripcion', [PostulanteController::class, 'mostrarFormularioPreinscripcion'])->name('preinscripcion.form');
Route::post('/preinscripcion', [PostulanteController::class, 'registrarPostulante'])->name('preinscripcion.registrar');
Route::get('/preinscripcion/exito', function () {
    if (!session('success')) {
        return redirect()->route('preinscripcion.form');
    }
    return view('preinscripcion.exito');
})->name('preinscripcion.exito');

// Rutas para recuperación de contraseña (simuladas)
Route::get('/forgot-password', fn() => view('auth.forgot_password'));
Route::post('/forgot-password', fn() => back()->with('status', 'sent'));

Route::get('/pago/{codigo}', [PostulanteController::class, 'mostrarPago'])->name('pago.index');
Route::post('/pago/{codigo}/confirmar', [PostulanteController::class, 'confirmarPago'])->name('pago.confirmar');

/*
|--------------------------------------------------------------------------
| 2. RUTAS PROTEGIDAS (Requieren Autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/logout-confirm', function () {
        return view('auth.logout');
    });
    Route::post('/logout', [UsuarioController::class, 'cerrarSesión'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |------------------------------------------------------------------
    | GESTIÓN DE USUARIOS (Sistema y Administrador)
    |------------------------------------------------------------------*/
    Route::middleware('privilegio:usuarios.ver')->group(function () {
        Route::get('/admin/usuarios', [UsuarioController::class, 'listarUsuarios'])->name('usuarios.index');
    });
    Route::middleware('privilegio:usuarios.editar')->group(function () {
        Route::get('/admin/usuarios/{id}/editar', [UsuarioController::class, 'editarUsuario'])->name('usuarios.edit');
        Route::put('/admin/usuarios/{id}', [UsuarioController::class, 'actualizarUsuario'])->name('usuarios.update');
    });
    Route::middleware('privilegio:usuarios.cargar')->group(function () {
        Route::get('/admin/usuarios/cargar', [UsuarioController::class, 'mostrarCargaMasiva'])->name('usuarios.cargar');
        Route::post('/admin/usuarios/cargar', [UsuarioController::class, 'procesarCargaMasiva'])->name('usuarios.procesarCarga');
    });
    Route::middleware('privilegio:perfiles.gestionar')->group(function () {
        Route::get('/admin/perfiles', [UsuarioController::class, 'gestionarPerfiles'])->name('perfiles.index');
        Route::put('/admin/perfiles/{id}/privilegios', [UsuarioController::class, 'actualizarPrivilegios'])->name('perfiles.privilegios');
    });
    

    /*
    |------------------------------------------------------------------
    | GESTIÓN DE POSTULANTES
    |------------------------------------------------------------------*/
    Route::middleware('privilegio:postulantes.ver')->group(function () {
        Route::get('/admin/postulantes', [PostulanteController::class, 'listarPostulantes'])->name('postulantes.index');
        Route::get('/admin/postulantes/{codigo}', [PostulanteController::class, 'verPostulante'])->name('postulantes.show');
    });
    Route::middleware('privilegio:postulantes.editar')->group(function () {
        Route::get('/admin/postulantes/{codigo}/editar', [PostulanteController::class, 'editarPostulante'])->name('postulantes.edit');
        Route::put('/admin/postulantes/{codigo}', [PostulanteController::class, 'actualizarPostulante'])->name('postulantes.update');
        Route::patch('/admin/postulantes/{codigo}/requisitos', [PostulanteController::class, 'actualizarRequisitos'])->name('postulantes.requisitos');
        Route::post('/admin/postulantes/{codigo}/baja', [PostulanteController::class, 'darBaja'])->name('postulantes.baja');
        Route::patch('/admin/postulantes/{codigo}/desactivar', [PostulanteController::class, 'desactivarPostulante'])->name('postulantes.desactivar');
    });

    /*
    |------------------------------------------------------------------
    | MÓDULO DOCENTE / EVALUACIÓN
    |------------------------------------------------------------------*/
    Route::middleware('privilegio:notas.ver')->group(function () {
        Route::get('/docente/registrar-notas', [ExamenController::class, 'obtenerGrupoYMaterias'])->name('notas.index');
        Route::get('/docente/grupos-materias', [ExamenController::class, 'obtenerGrupoYMaterias']);
        Route::get('/docente/grupos/{id_grupo}/postulantes', [ExamenController::class, 'obtenerPostulantes']);
    });
    Route::middleware('privilegio:notas.registrar')->group(function () {
        Route::post('/docente/registrar-notas', [ExamenController::class, 'registrarNotas'])->name('notas.registrar');
    });

    /*
    |------------------------------------------------------------------
    | GESTIÓN DE DOCENTES Y PERSONAL ADMINISTRATIVO
    |------------------------------------------------------------------*/
    Route::middleware('privilegio:personal.crear')->group(function () {
        Route::get('/admin/personal/crear', [PersonalController::class, 'crearDocente'])->name('personal.crear');
        Route::post('/admin/personal', [PersonalController::class, 'guardarDocente'])->name('personal.guardar');
    });
    Route::middleware('privilegio:personal.ver')->group(function () {
        Route::get('/admin/personal', [PersonalController::class, 'listarDocentes'])->name('personal.index');
        Route::get('/admin/personal/{registro}', [PersonalController::class, 'verDocente'])->name('personal.show');
    });
    Route::middleware('privilegio:personal.editar')->group(function () {
        Route::put('/admin/personal/{registro}', [PersonalController::class, 'actualizarDocente'])->name('personal.actualizar');
        Route::get('/admin/personal/{registro}/editar', [PersonalController::class, 'editarDocente'])->name('personal.edit');
    });
    Route::middleware('privilegio:personal.desactivar')->group(function () {
        Route::patch('/admin/personal/{registro}/desactivar', [PersonalController::class, 'desactivarDocente'])->name('personal.desactivar');
        Route::patch('/admin/personal/{registro}/activar', [PersonalController::class, 'activarDocente'])->name('personal.activar');
    });

    /*
    |------------------------------------------------------------------
    | GESTIÓN DE CARRERAS Y CONFIGURACIÓN DE CUPOS
    |------------------------------------------------------------------*/
    Route::middleware('privilegio:carreras.ver')->group(function () {
        Route::get('/admin/carreras', [CarreraController::class, 'listarCarreras'])->name('carreras.index');
        Route::get('/admin/carreras-cupos', [CarreraController::class, 'listarCarreras']);
    });
    Route::middleware('privilegio:cupos.editar')->group(function () {
        Route::post('/admin/carreras/cupos', [CarreraController::class, 'guardarCupos']);
        Route::put('/admin/carreras/cupos/{id_carrera_gestion}', [CarreraController::class, 'actualizarCupos']);
        Route::post('/admin/carreras-cupos/guardar-masivo', [CarreraController::class, 'guardarMasivo'])->name('carreras.guardarMasivo');
        Route::post('/admin/carreras-cupos/guardar-fila', [CarreraController::class, 'guardarCuposFila'])->name('carreras.guardarFila');
    });

    /*
    |------------------------------------------------------------------
    | AUDITORÍA DE SEGURIDAD (Bitácora del Sistema)
    |------------------------------------------------------------------*/
    Route::middleware('privilegio:bitacora.ver')->group(function () {
        Route::get('/admin/bitacora', [BitacoraController::class, 'listarEventos'])->name('bitacora.index');
        Route::get('/admin/bitacora/{id}', [BitacoraController::class, 'obtenerDetalle'])->name('bitacora.show');
    });
});