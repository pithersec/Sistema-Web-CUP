@extends('layouts.app')

@section('title', 'Cargar Cuentas Masivas - CUP')
@section('page_title', 'Cargar Cuentas Masivas')

@section('content')
<style>
    .cargar-wrapper {
        font-family: 'Source Sans 3', sans-serif;
    }

    .dos-columnas {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: start;
    }

    @media (max-width: 768px) {
        .dos-columnas { grid-template-columns: 1fr; }
    }

    .card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 16px;
    }

    .card-header {
        padding: 14px 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .card-header h2 {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 14px;
        margin: 0;
    }

    .card-header .subtitulo {
        font-size: 11px;
        color: #8aa0b8;
        margin-top: 2px;
        font-family: 'Source Sans 3', sans-serif;
    }

    .card-body { padding: 20px; }

    .formato-info {
        background: #f0f7ff;
        border-left: 3px solid #1a5fa8;
        border-radius: 0 6px 6px 0;
        padding: 12px 14px;
        margin-bottom: 16px;
        font-size: 12px;
        color: #1a3a5c;
    }

    .formato-info p { margin: 0 0 6px 0; font-weight: 600; }

    .columnas {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 8px;
    }

    .col-tag {
        background: white;
        border: 1px solid #b3d4f5;
        color: #1a5fa8;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }

    .col-tag.opcional {
        border-style: dashed;
        color: #5a8ab0;
    }

    .valores {
        font-size: 12px;
        color: #5a5a5a;
        line-height: 1.7;
        margin-bottom: 14px;
    }

    .valores strong { color: #0d3b6e; }

    .nota {
        border-radius: 0 6px 6px 0;
        padding: 10px 14px;
        font-size: 11.5px;
        margin-bottom: 16px;
    }

    .nota-amarilla { background: #fffbea; border-left: 3px solid #f0a500; color: #7a5800; }
    .nota-azul     { background: #f0f7ff; border-left: 3px solid #1a5fa8; color: #1a3a5c; }

    .upload-area {
        border: 2px dashed #b3d4f5;
        border-radius: 8px;
        padding: 24px 16px;
        text-align: center;
        background: #f8fbff;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
        margin-bottom: 14px;
    }

    .upload-area:hover { border-color: #1a5fa8; background: #f0f7ff; }

    .upload-area input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    .upload-icon { font-size: 26px; margin-bottom: 6px; }
    .upload-area p { margin: 0; font-size: 13px; color: #4a6a8a; }
    .upload-area .hint { font-size: 11px; color: #8aa0b8; margin-top: 4px; }

    .nombre-archivo {
        font-size: 12px;
        color: #1a7a3c;
        font-weight: 600;
        margin-top: 8px;
        display: none;
    }

    .btn-primary {
        width: 100%;
        padding: 10px;
        background: #0d3b6e;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Source Sans 3', sans-serif;
        margin-bottom: 8px;
    }

    .btn-primary:hover { background: #0a2d56; }

    .btn-secondary {
        width: 100%;
        padding: 9px;
        background: white;
        color: #5a5a5a;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: block;
        text-align: center;
        font-family: 'Source Sans 3', sans-serif;
    }

    .btn-secondary:hover { background: #f8fafc; }

    .alert-error {
        background: #fde8e8;
        color: #c0392b;
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 16px;
    }
</style>

@if($errors->any())
<div class="alert-error">{{ $errors->first() }}</div>
@endif

<div class="cargar-wrapper">
    <a href="{{ route('personal.index') }}" style="display:inline-block; padding:9px 20px; background:#cbd5e1; color:#1e293b; border-radius:6px; font-size:13px; font-weight:600; text-decoration:none; margin-bottom:20px;">← Volver a Personal</a>

    <div class="dos-columnas">

        {{-- COLUMNA 1: PERSONAL --}}
        <div>
            <div class="card">
                <div class="card-header">
                    <h2>Personal</h2>
                    <div class="subtitulo">Datos personales y cuentas de usuario</div>
                </div>
                <div class="card-body">
                    <div class="formato-info">
                        <p>Columnas requeridas:</p>
                        <div class="columnas">
                            <span class="col-tag">ci</span>
                            <span class="col-tag">nombre</span>
                            <span class="col-tag">apellido</span>
                            <span class="col-tag">genero</span>
                            <span class="col-tag opcional">telefono</span>
                            <span class="col-tag">correo</span>
                            <span class="col-tag">fecha nacimiento</span>
                            <span class="col-tag">direccion</span>
                            <span class="col-tag">perfil</span>
                        </div>
                    </div>

                    <div class="valores">
                        <strong>genero:</strong> <code>Masculino</code> / <code>Femenino</code><br>
                        <strong>perfil:</strong> <code>Administrador</code> / <code>Docente</code><br>
                        <strong>fecha nacimiento:</strong> <code>DD/MM/YYYY</code><br>
                        <strong>telefono:</strong> opcional
                    </div>

                    <div class="nota nota-amarilla">
                        ⚠️ CI duplicados serán omitidos y reportados.
                    </div>

                    <form action="{{ route('usuarios.procesarCarga') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="upload-area">
                            <input type="file" name="archivo" accept=".xlsx,.xls,.csv"
                                    onchange="mostrarNombre(this, 'nombre-personal')">
                            <div class="upload-icon">📂</div>
                            <p>Seleccionar archivo</p>
                            <p class="hint">.xlsx, .xls, .csv — Máx. 5MB</p>
                            <div id="nombre-personal" class="nombre-archivo"></div>
                        </div>
                        <button type="submit" class="btn-primary">Procesar personal</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- COLUMNA 2: REQUISITOS --}}
        <div>
            <div class="card">
                <div class="card-header">
                    <h2>Requisitos del personal</h2>
                    <div class="subtitulo">Áreas y credenciales académicas</div>
                </div>
                <div class="card-body">
                    <div class="formato-info">
                        <p>Columnas requeridas:</p>
                        <div class="columnas">
                            <span class="col-tag">ci</span>
                            <span class="col-tag">area</span>
                            <span class="col-tag">nivel grado</span>
                            <span class="col-tag">nivel experiencia</span>
                            <span class="col-tag">maestria</span>
                            <span class="col-tag">doctorado</span>
                            <span class="col-tag">diplomado</span>
                        </div>
                    </div>

                    <div class="valores">
                        <strong>area:</strong> <code>matematicas</code> / <code>fisica</code> / <code>computacion</code> / <code>ingles</code> / <code>administracion</code> / <code>sistemas</code> / <code>otra</code><br>
                        <strong>nivel grado:</strong> <code>tecnico_medio</code> / <code>tecnico_superior</code> / <code>licenciatura</code> / <code>ingenieria</code> / <code>maestria</code> / <code>doctorado</code><br>
                        <strong>nivel experiencia:</strong> número entero (años)<br>
                        <strong>maestria / doctorado / diplomado:</strong> <code>SI</code> / <code>NO</code>
                    </div>

                    <div class="nota nota-azul">
                        💡 Un CI puede repetirse para registrar múltiples áreas.
                    </div>

                    <form action="{{ route('usuarios.procesarRequisitos') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="upload-area">
                            <input type="file" name="archivo_requisitos" accept=".xlsx,.xls,.csv"
                                    onchange="mostrarNombre(this, 'nombre-requisitos')">
                            <div class="upload-icon">📂</div>
                            <p>Seleccionar archivo</p>
                            <p class="hint">.xlsx, .xls, .csv — Máx. 5MB</p>
                            <div id="nombre-requisitos" class="nombre-archivo"></div>
                        </div>
                        <button type="submit" class="btn-primary">Procesar requisitos</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function mostrarNombre(input, targetId) {
    const label = document.getElementById(targetId);
    if (input.files && input.files[0]) {
        label.textContent = '✔ ' + input.files[0].name;
        label.style.display = 'block';
    }
}
</script>
@endsection