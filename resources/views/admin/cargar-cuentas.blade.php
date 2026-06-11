@extends('layouts.app')

@section('title', 'Cargar Cuentas Masivas - CUP')
@section('page_title', 'Cargar Cuentas Masivas')

@section('content')
<style>
    .cargar-wrapper {
        max-width: 700px;
        font-family: 'Source Sans 3', sans-serif;
        margin: auto;
    }

    .card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .card-header {
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    .card-header h2 {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 15px;
        margin: 0;
    }

    .card-body { padding: 24px; }

    .formato-info {
        background: #f0f7ff;
        border-left: 4px solid #1a5fa8;
        border-radius: 0 6px 6px 0;
        padding: 14px 18px;
        margin-bottom: 24px;
        font-size: 13px;
        color: #1a3a5c;
    }

    .formato-info p { margin: 0 0 8px 0; font-weight: 600; }

    .columnas {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
    }

    .col-tag {
        background: white;
        border: 1px solid #b3d4f5;
        color: #1a5fa8;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .col-tag.opcional {
        border-style: dashed;
        color: #5a8ab0;
    }

    .upload-area {
        border: 2px dashed #b3d4f5;
        border-radius: 8px;
        padding: 32px;
        text-align: center;
        background: #f8fbff;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
        margin-bottom: 20px;
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

    .upload-icon { font-size: 32px; margin-bottom: 8px; }
    .upload-area p { margin: 0; font-size: 14px; color: #4a6a8a; }
    .upload-area .hint { font-size: 12px; color: #8aa0b8; margin-top: 6px; }

    #nombre-archivo {
        font-size: 13px;
        color: #1a7a3c;
        font-weight: 600;
        margin-top: 10px;
        display: none;
    }

    .btn-primary {
        padding: 10px 28px;
        background: #0d3b6e;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Source Sans 3', sans-serif;
    }

    .btn-primary:hover { background: #0a2d56; }

    .btn-secondary {
        padding: 10px 20px;
        background: white;
        color: #5a5a5a;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-family: 'Source Sans 3', sans-serif;
    }

    .btn-secondary:hover { background: #f8fafc; }

    .form-actions { display: flex; gap: 10px; align-items: center; }

    .alert-error {
        background: #fde8e8;
        color: #c0392b;
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .nota-perfiles {
        background: #fffbea;
        border-left: 4px solid #f0a500;
        border-radius: 0 6px 6px 0;
        padding: 12px 16px;
        font-size: 12.5px;
        color: #7a5800;
        margin-top: 16px;
    }

    .resumen-counters {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .counter-badge {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
    }

    .counter-creados  { background: #d4f5e2; color: #1a7a3c; }
    .counter-omitidos { background: #fff3cd; color: #856404; }
    .counter-errores  { background: #fde8e8; color: #c0392b; }

    .resumen-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        margin-bottom: 14px;
    }

    .resumen-table thead { background: #0d3b6e; color: white; }
    .resumen-table th { padding: 8px 12px; text-align: left; font-size: 12px; }
    .resumen-table td { padding: 7px 12px; border-bottom: 1px solid #e2e8f0; color: #333; }

    .problemas-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .btn-volver {
        display: inline-block;
        padding: 9px 20px;
        background: #f1f5f9;
        color: #333;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 20px;
        font-family: 'Source Sans 3', sans-serif;
    }

    .btn-volver:hover { background: #e2e8f0; }
</style>

<div class="cargar-wrapper">

    @if($errors->any())
    <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h2>Formato requerido del archivo</h2>
        </div>
        <div class="card-body">
            <div class="formato-info">
                <p>El archivo debe contener las siguientes columnas en la primera fila (encabezados exactos):</p>
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

            <div style="font-size: 13px; color: #5a5a5a; line-height: 1.8;">
                <strong style="color: #0d3b6e;">Valores aceptados:</strong><br>
                — <strong>genero:</strong> <code>Masculino</code> / <code>Femenino</code><br>
                — <strong>perfil:</strong> <code>Administrador</code> / <code>Docente</code><br>
                — <strong>fecha nacimiento:</strong> formato <code>DD/MM/YYYY</code> (ej. <code>15/03/1990</code>)<br>
                — <strong>telefono:</strong> opcional, puede dejarse vacío
            </div>

            <div class="nota-perfiles">
                ⚠️ Los registros duplicados (mismo CI) y los perfiles inválidos serán omitidos y reportados en el resumen.
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Subir archivo</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('usuarios.procesarCarga') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="upload-area">
                    <input type="file" name="archivo" id="archivoInput"
                            accept=".xlsx,.xls,.csv"
                            onchange="mostrarNombre(this)">
                    <div class="upload-icon">📂</div>
                    <p>Haz clic para seleccionar el archivo</p>
                    <p class="hint">Formatos aceptados: .xlsx, .xls, .csv — Máximo 5MB</p>
                    <div id="nombre-archivo"></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Procesar archivo</button>
                    <a href="{{ route('personal.index') }}" class="btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function mostrarNombre(input) {
    const label = document.getElementById('nombre-archivo');
    if (input.files && input.files[0]) {
        label.textContent = '✔ ' + input.files[0].name;
        label.style.display = 'block';
    }
}
</script>

@endsection