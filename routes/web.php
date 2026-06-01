<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PostulanteController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\BitacoraController;

/*
|--------------------------------------------------------------------------
| 1. RUTAS PÚBLICAS (Invitados / Sin Autenticación)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// CU-01: Autenticar Usuario (Login)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [UsuarioController::class, 'enviarCredenciales']);

// CU-03: Realizar Preinscripción Digital (Acceso Postulantes)
Route::get('/preinscripcion', [PostulanteController::class, 'mostrarFormularioPreinscripcion']);
Route::post('/preinscripcion', [PostulanteController::class, 'registrarPostulante']);
Route::get('/preinscripcion/exito', function() {
    if (!session('success')) return redirect('/preinscripcion');
    return view('preinscripcion_exito');
});

/*
|--------------------------------------------------------------------------
| 2. RUTAS PROTEGIDAS (Requieren Autenticación mediante Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /* =====================================================================
     * CU-02: SECCIÓN DE CIERRE DE SESIÓN
     * ===================================================================== */
    Route::get('/logout-confirm', function () {
        return view('auth.logout');
    });
    Route::post('/logout', [UsuarioController::class, 'cerrarSesión']);


    /* =====================================================================
     * PANEL PRINCIPAL / PORTAL INTERNO (Dashboard)
     * ===================================================================== */
    Route::get('/dashboard', [DashboardController::class, 'obtenerIndicadores']);


    /* =====================================================================
     * GESTIÓN DE POSTULANTES (Ventanilla / Control de Admisión)
     * ===================================================================== */
    Route::get('/admin/postulantes', [PostulanteController::class, 'listarPostulantes']);
    Route::put('/admin/postulantes/{codigo}', [PostulanteController::class, 'actualizarPostulante']);
    Route::post('/admin/postulantes/{codigo}/baja', [PostulanteController::class, 'darBaja']); // Método unificado para baja segura
    Route::patch('/admin/postulantes/{codigo}/desactivar', [PostulanteController::class, 'desactivarPostulante']);


    /* =====================================================================
     * MÓDULO DOCENTE / EVALUACIÓN (Registro Estadístico de Notas)
     * ===================================================================== */
    Route::get('/docente/grupos-materias', [ExamenController::class, 'obtenerGrupoYMaterias']);
    Route::get('/docente/grupos/{id_grupo}/postulantes', [ExamenController::class, 'obtenerPostulantes']);
    Route::post('/docente/registrar-notas', [ExamenController::class, 'registrarNotas']);


    /* =====================================================================
     * GESTIÓN DE DOCENTES Y PERSONAL ADMINISTRATIVO
     * ===================================================================== */
    Route::get('/admin/docentes', [PersonalController::class, 'listarDocentes']);
    Route::post('/admin/docentes', [PersonalController::class, 'guardarDocente']);
    Route::put('/admin/docentes/{registro}', [PersonalController::class, 'actualizarDocente']);
    Route::post('/admin/docentes/{registro}/baja', [PersonalController::class, 'desactivarDocente']);


    /* =====================================================================
     * CU-15: GESTIÓN DE CARRERAS Y CONFIGURACIÓN DE CUPOS
     * ===================================================================== */
    Route::get('/admin/carreras', [CarreraController::class, 'listarCarreras']);
    Route::get('/admin/carreras-cupos', [CarreraController::class, 'listarCarreras']); // Alias compatible con tus vistas
    Route::post('/admin/carreras/cupos', [CarreraController::class, 'guardarCupos']);
    Route::put('/admin/carreras/cupos/{id_carrera_gestion}', [CarreraController::class, 'actualizarCupos']);
    Route::post('/admin/carreras-cupos/guardar-masivo', [CarreraController::class, 'guardarMasivo']);
    Route::post('/admin/carreras-cupos/guardar-fila', [CarreraController::class, 'guardarCuposFila']);


    /* =====================================================================
     * CU-16: GESTIÓN DE USUARIOS Y CONTROL DE PERFILES
     * ===================================================================== */
    Route::get('/admin/usuarios', [UsuarioController::class, 'listarUsuarios']);
    Route::post('/admin/usuarios', [UsuarioController::class, 'guardarUsuario']);
    Route::put('/admin/usuarios/{id}', [UsuarioController::class, 'actualizarUsuario']);
    Route::delete('/admin/usuarios/{id}', [UsuarioController::class, 'eliminarUsuario']);
    Route::get('/admin/perfiles', [UsuarioController::class, 'gestionarPerfiles']);


    /* =====================================================================
     * CU-19: AUDITORÍA DE SEGURIDAD (Bitácora del Sistema)
     * ===================================================================== */
    Route::get('/admin/bitacora', [BitacoraController::class, 'listarEventos']);
    Route::get('/admin/bitacora/{id}', [BitacoraController::class, 'obtenerDetalle']);

});
