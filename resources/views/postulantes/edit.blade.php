@extends('layouts.app')

@section('title', 'Editar Postulante - CUP')
@section('page_title', 'Editar Postulante')

@section('content')
<style>
    .edit-wrapper {
        width: 100%;
        max-width: 900px;
        font-family: 'Source Sans 3', sans-serif;
    }

    .postulante-header {
        background: white;
        border-radius: 10px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .postulante-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .avatar-circle {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #0d3b6e;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Merriweather', serif;
        font-size: 20px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .postulante-header-info h2 {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 18px;
        margin-bottom: 4px;
    }

    .postulante-header-info p {
        color: #5a5a5a;
        font-size: 13px;
    }

    .header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .btn-cancel {
        padding: 9px 18px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        background: white;
        color: #5a5a5a;
        font-family: 'Source Sans 3', sans-serif;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-cancel:hover { background: #f8fafc; }

    .btn-save {
        padding: 9px 18px;
        border: none;
        border-radius: 6px;
        background: #0d3b6e;
        color: white;
        font-family: 'Source Sans 3', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-save:hover { background: #1a5fa8; }

    .section-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        margin-bottom: 16px;
        overflow: hidden;
    }

    .section-card-header {
        background: #0d3b6e;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-card-header h3 {
        color: white;
        font-family: 'Merriweather', serif;
        font-size: 13px;
    }

    .section-card-body { padding: 20px; }

    .fields-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .form-group { margin-bottom: 0; }

    .form-group label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #5a5a5a;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin-bottom: 6px;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-family: 'Source Sans 3', sans-serif;
        font-size: 14px;
        color: #333;
        background: white;
        outline: none;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus { border-color: #1a5fa8; }

    .form-group input[readonly] {
        background: #f8fafc;
        color: #888;
        cursor: not-allowed;
    }

    .field-hint {
        font-size: 11px;
        color: #aaa;
        margin-top: 4px;
    }

    .has-error { border-color: #c0392b !important; background: #fdf0f0 !important; }
    .field-error { color: #c0392b; font-size: 12px; margin-top: 4px; }

    .alert-success {
        background: #d4f5e2;
        color: #1a7a3c;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 16px;
        font-size: 13.5px;
        font-weight: 600;
    }

    /* BADGES */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-green  { background: #d4f5e2; color: #1a7a3c; }
    .badge-red    { background: #fde8e8; color: #c0392b; }
    .badge-yellow { background: #fef9e7; color: #d68910; }
    .badge-blue   { background: #dceeff; color: #1a5fa8; }
    .badge-dark   { background: #e2e2e2; color: #1a1a1a; }
    .badge-gray   { background: #f1f5f9; color: #5a5a5a; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .fields-grid { grid-template-columns: repeat(2, 1fr); }
        .postulante-header { flex-direction: column; align-items: flex-start; }
        .header-actions { width: 100%; justify-content: flex-end; }
        .btn-cancel, .btn-save { text-align: center; white-space: nowrap; display: inline-block; flex: 1; }
    }

    @media (max-width: 480px) {
        .fields-grid { grid-template-columns: 1fr; }
    }
</style>

@if(session('success'))
<div class="alert-success">{{ session('success') }}</div>
@endif

<div class="edit-wrapper">

    <form action="{{ route('postulantes.update', $postulante->codigo) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- HEADER --}}
        <div class="postulante-header">
            <div class="postulante-header-left">
                <div class="avatar-circle">
                    {{ strtoupper(substr($postulante->datosPersonales->nombre ?? 'P', 0, 1)) }}
                </div>
                <div class="postulante-header-info">
                    <h2>{{ $postulante->datosPersonales->nombre ?? 'N/A' }} {{ $postulante->datosPersonales->apellido ?? '' }}</h2>
                    <p>Código: <strong>{{ $postulante->codigo }}</strong> &nbsp;·&nbsp;
                        @if($postulante->estado == 'aprobado')
                            <span class="badge badge-green">Aprobado</span>
                        @elseif($postulante->estado == 'reprobado')
                            <span class="badge badge-red">Reprobado</span>
                        @elseif($postulante->estado == 'inscrito')
                            <span class="badge badge-blue">Inscrito</span>
                        @elseif($postulante->estado == 'preinscrito')
                            <span class="badge badge-yellow">Preinscrito</span>
                        @elseif($postulante->estado == 'baja')
                            <span class="badge badge-dark">Baja</span>
                        @else
                            <span class="badge badge-gray">{{ $postulante->estado }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('postulantes.show', $postulante->codigo) }}" class="btn-cancel">← Cancelar</a>
                <button type="submit" class="btn-save">💾 Guardar Cambios</button>
            </div>
        </div>

        {{-- DATOS PERSONALES --}}
        <div class="section-card">
            <div class="section-card-header">
                <span>👤</span>
                <h3>Datos Personales</h3>
            </div>
            <div class="section-card-body">
                <div class="fields-grid">
                    <div class="form-group">
                        <label>Cédula de Identidad</label>
                        <input type="text" value="{{ $postulante->ci }}" readonly />
                        <p class="field-hint">No editable</p>
                    </div>
                    <div class="form-group">
                        <label>Nombre(s) <span style="color:#c0392b">*</span></label>
                        <input type="text" name="nombre"
                            value="{{ old('nombre', $postulante->datosPersonales->nombre ?? '') }}"
                            class="{{ $errors->has('nombre') ? 'has-error' : '' }}"
                            required />
                        @if($errors->has('nombre')) <div class="field-error">{{ $errors->first('nombre') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Apellido(s) <span style="color:#c0392b">*</span></label>
                        <input type="text" name="apellido"
                            value="{{ old('apellido', $postulante->datosPersonales->apellido ?? '') }}"
                            class="{{ $errors->has('apellido') ? 'has-error' : '' }}"
                            required />
                        @if($errors->has('apellido')) <div class="field-error">{{ $errors->first('apellido') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Género <span style="color:#c0392b">*</span></label>
                        <select name="genero" class="{{ $errors->has('genero') ? 'has-error' : '' }}" required>
                            <option value="m" {{ old('genero', $postulante->datosPersonales->genero ?? '') == 'm' ? 'selected' : '' }}>Masculino</option>
                            <option value="f" {{ old('genero', $postulante->datosPersonales->genero ?? '') == 'f' ? 'selected' : '' }}>Femenino</option>
                        </select>
                        @if($errors->has('genero')) <div class="field-error">{{ $errors->first('genero') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Fecha de Nacimiento <span style="color:#c0392b">*</span></label>
                        <input type="date" name="fecha_nac"
                            value="{{ old('fecha_nac', $postulante->datosPersonales->fecha_nac ? \Carbon\Carbon::parse($postulante->datosPersonales->fecha_nac)->format('Y-m-d') : '') }}"
                            class="{{ $errors->has('fecha_nac') ? 'has-error' : '' }}"
                            required />
                        @if($errors->has('fecha_nac')) <div class="field-error">{{ $errors->first('fecha_nac') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Procedencia</label>
                        <input type="text" name="procedencia"
                            value="{{ old('procedencia', $postulante->procedencia ?? '') }}" />
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono"
                            value="{{ old('telefono', $postulante->datosPersonales->telefono ?? '') }}" />
                    </div>
                    <div class="form-group">
                        <label>Teléfono 2</label>
                        <input type="text" name="telefono_2"
                            value="{{ old('telefono_2', $postulante->telefono_2 ?? '') }}" />
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico <span style="color:#c0392b">*</span></label>
                        <input type="email" name="correo"
                            value="{{ old('correo', $postulante->datosPersonales->correo ?? '') }}"
                            class="{{ $errors->has('correo') ? 'has-error' : '' }}"
                            required />
                        @if($errors->has('correo')) <div class="field-error">{{ $errors->first('correo') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Dirección <span style="color:#c0392b">*</span></label>
                        <input type="text" name="direccion"
                            value="{{ old('direccion', $postulante->datosPersonales->direccion ?? '') }}"
                            class="{{ $errors->has('direccion') ? 'has-error' : '' }}"
                            required />
                        @if($errors->has('direccion')) <div class="field-error">{{ $errors->first('direccion') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Año de Egreso de Bachiller</label>
                        <input type="text" name="gestion_egreso"
                            value="{{ old('gestion_egreso', $postulante->gestion_egreso ?? '') }}" />
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection
