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

// Cambia esto de la línea 19 a la 21:
Route::get('/', function () {
    return redirect()->route('login');
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

//CU3 - Realizar Preinscripcion
// 1. Muestra el formulario de preinscripción (Público)
Route::get('/preinscripcion', [PostulanteController::class, 'mostrarFormularioPreinscripcion']);

// 2. Procesa los datos enviados por el estudiante (Guarda en la BD)
Route::post('/preinscripcion', [PostulanteController::class, 'registrarPostulante']);

// 3. Pantalla de éxito tras terminar el registro
Route::get('/preinscripcion/exito', function() {
    if (!session('success')) return redirect('/preinscripcion');
    return view('Preinscripcion.exito'); // Apunta a views/Preinscripcion/exito.blade.php
});



// Ruta para CARGAR y FILTRAR la planilla de notas (Método GET)
Route::get('/docente/registrar-notas', [ExamenController::class, 'obtenerGrupoYMaterias']);

// Ruta para GUARDAR las notas masivamente (Método POST)
Route::post('/docente/registrar-notas', [ExamenController::class, 'registrarNotas']);


// Asegúrate de que apunten exactamente al método que tienes en tu controlador
Route::get('/docente/registrar-notas', [ExamenController::class, 'obtenerGrupoYMaterias']);
Route::post('/docente/registrar-notas', [ExamenController::class, 'registrarNotas']);



Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');


// Apuntando directo al método index
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');




// Ruta para mostrar la tabla con filtros
Route::get('/admin/postulantes', [PostulanteController::class, 'listarPostulantes'])->name('postulantes.index');

// Ruta específica para procesar el botón "Baja"
Route::post('/admin/postulantes/{codigo}/baja', [PostulanteController::class, 'darBaja'])->name('postulantes.baja');




Route::middleware(['auth'])->group(function () {
    // Listado principal con filtros
    Route::get('/admin/docentes', [PersonalController::class, 'listarDocentes'])->name('docentes.index');

    // Desactivación o baja lógica (se usa PATCH porque actualiza un campo específico del modelo)
    Route::patch('/admin/docentes/{registro}/desactivar', [PersonalController::class, 'desactivarDocente'])->name('docentes.desactivar');
    
    // Las rutas complementarias para guardar y actualizar (vistas auxiliares)
    Route::post('/admin/docentes/guardar', [PersonalController::class, 'guardarDocente'])->name('docentes.guardar');
    Route::put('/admin/docentes/{registro}/actualizar', [PersonalController::class, 'actualizarDocente'])->name('docentes.actualizar');
});



Route::middleware(['auth'])->group(function () {
    // 1. Mostrar la matriz filtrada por gestión
    Route::get('/admin/carreras-cupos', [CarreraController::class, 'listarCarreras'])->name('carreras.index');

    // 2. Acción del botón masivo inferior (Guardar Cambios)
    Route::post('/admin/carreras-cupos/masivo', [CarreraController::class, 'guardarMasivo'])->name('carreras.guardarMasivo');

    // 3. Acción del botón "Guardar" de cada fila individual
    Route::post('/admin/carreras-cupos/fila', [CarreraController::class, 'guardarCuposFila'])->name('carreras.guardarFila');
});



Route::middleware(['auth'])->group(function () {
    // Listar usuarios con filtros
    Route::get('/admin/usuarios', [UsuarioController::class, 'listarUsuarios'])->name('usuarios.index');

    // Procesar envío de nueva cuenta
    Route::post('/admin/usuarios/guardar', [UsuarioController::class, 'guardarUsuario'])->name('usuarios.store');

    // Procesar actualización de cuenta existente
    Route::put('/admin/usuarios/{id}/actualizar', [UsuarioController::class, 'actualizarUsuario'])->name('usuarios.update');

    // Eliminar registro físico por ID (Usa DELETE)
    Route::delete('/admin/usuarios/{id}', [UsuarioController::class, 'eliminarUsuario'])->name('usuarios.destroy');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/admin/bitacora', [BitacoraController::class, 'listarEventos'])->name('bitacora.index');
});
