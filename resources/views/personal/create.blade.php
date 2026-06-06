@extends('layouts.app')

@section('title', 'Registrar Personal - CUP')
@section('page_title', 'Registrar Personal')

@section('content')
<style>
    .create-wrapper {
        width: 100%;
        max-width: 900px;
        font-family: 'Source Sans 3', sans-serif;
    }

    .page-header {
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

    .page-header-info h2 {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 18px;
        margin-bottom: 4px;
    }

    .page-header-info p {
        color: #5a5a5a;
        font-size: 13px;
    }

    .header-actions { display: flex; gap: 10px; align-items: center; }

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
        display: inline-block;
        text-align: center;
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
        text-align: center;
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

    .fields-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
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

    .field-hint { font-size: 11px; color: #aaa; margin-top: 4px; }
    .has-error { border-color: #c0392b !important; background: #fdf0f0 !important; }
    .field-error { color: #c0392b; font-size: 12px; margin-top: 4px; }

    /* CREDENCIALES */
    .credencial-row {
        display: grid;
        grid-template-columns: repeat(6, 1fr) auto;
        gap: 10px;
        align-items: end;
        padding: 14px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        margin-bottom: 10px;
    }

    .btn-remove-row {
        padding: 10px 12px;
        border: none;
        border-radius: 6px;
        background: #fde8e8;
        color: #c0392b;
        font-size: 14px;
        cursor: pointer;
    }
    .btn-remove-row:hover { background: #fbcbcb; }

    .btn-add-row {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border: 1.5px dashed #1a5fa8;
        border-radius: 6px;
        background: white;
        color: #1a5fa8;
        font-family: 'Source Sans 3', sans-serif;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
        transition: background 0.2s;
    }
    .btn-add-row:hover { background: #f0f8ff; }

    /* INFO BOX cuenta */
    .info-box {
        background: #f0f8ff;
        border: 1.5px solid #dceeff;
        border-radius: 8px;
        padding: 14px 16px;
        font-size: 13px;
        color: #1a5fa8;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .info-box-icon { font-size: 18px; flex-shrink: 0; }

    /* SECCION REQUISITOS — oculta por defecto */
    #seccion-requisitos { display: none; }

    @media (max-width: 768px) {
        .fields-grid { grid-template-columns: repeat(2, 1fr); }
        .fields-grid-2 { grid-template-columns: 1fr; }
        .page-header { flex-direction: column; align-items: flex-start; }
        .header-actions { width: 100%; justify-content: flex-end; }
        .btn-cancel, .btn-save { text-align: center; }
        .credencial-row { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 480px) {
        .fields-grid { grid-template-columns: 1fr; }
        .credencial-row { grid-template-columns: 1fr; }
    }
</style>

@if(session('success'))
<div style="background:#d4f5e2; color:#1a7a3c; padding:12px 16px; border-radius:6px; margin-bottom:16px; font-size:13.5px; font-weight:600; width:100%;">
    ✓ {{ session('success') }}
</div>
@endif

@if($errors->has('error'))
<div style="background:#fde8e8; color:#c0392b; padding:12px 16px; border-radius:6px; margin-bottom:16px; font-size:13.5px; font-weight:600; width:100%;">
    {{ $errors->first('error') }}
</div>
@endif

<div class="create-wrapper">
    <form action="{{ route('docentes.guardar') }}" method="POST">
        @csrf

        {{-- HEADER --}}
        <div class="page-header">
            <div class="page-header-info">
                <h2>Nuevo Registro de Personal</h2>
                <p>Complete los datos del personal a registrar en el sistema.</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('docentes.index') }}" class="btn-cancel">← Cancelar</a>
                <button type="submit" class="btn-save">💾 Registrar Personal</button>
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
                        <label>Cédula de Identidad <span style="color:#c0392b">*</span></label>
                        <input type="text" name="ci"
                            value="{{ old('ci') }}"
                            class="{{ $errors->has('ci') ? 'has-error' : '' }}"
                            required 
                            placeholder="Ej: 5432100"/>
                        @if($errors->has('ci')) <div class="field-error">{{ $errors->first('ci') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Nombre(s) <span style="color:#c0392b">*</span></label>
                        <input type="text" name="nombre"
                            value="{{ old('nombre') }}"
                            class="{{ $errors->has('nombre') ? 'has-error' : '' }}"
                            required 
                            placeholder="Ej: Carlos"/>
                        @if($errors->has('nombre')) <div class="field-error">{{ $errors->first('nombre') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Apellido(s) <span style="color:#c0392b">*</span></label>
                        <input type="text" name="apellido"
                            value="{{ old('apellido') }}"
                            class="{{ $errors->has('apellido') ? 'has-error' : '' }}"
                            required 
                            placeholder="Ej: Pérez Ramos"/>
                        @if($errors->has('apellido')) <div class="field-error">{{ $errors->first('apellido') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Género <span style="color:#c0392b">*</span></label>
                        <select name="genero" class="{{ $errors->has('genero') ? 'has-error' : '' }}" required>
                            <option value="">-- Seleccionar --</option>
                            <option value="m" {{ old('genero') == 'm' ? 'selected' : '' }}>Masculino</option>
                            <option value="f" {{ old('genero') == 'f' ? 'selected' : '' }}>Femenino</option>
                        </select>
                        @if($errors->has('genero')) <div class="field-error">{{ $errors->first('genero') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" />
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="Ej: 71234567"/>
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico <span style="color:#c0392b">*</span></label>
                        <input type="email" name="correo"
                            value="{{ old('correo') }}"
                            class="{{ $errors->has('correo') ? 'has-error' : '' }}"
                            required 
                            placeholder="Ej: carlos.perez@ficct.uagrm.edu.bo"/>
                        @if($errors->has('correo')) <div class="field-error">{{ $errors->first('correo') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion') }}" placeholder="Ej: Av. Busch, 2do Anillo"/>
                    </div>
                </div>
            </div>
        </div>

        {{-- DATOS DEL REGISTRO --}}
        <div class="section-card">
            <div class="section-card-header">
                <span>🏷️</span>
                <h3>Datos del Registro</h3>
            </div>
            <div class="section-card-body">
                <div class="fields-grid-2">
                    <div class="form-group">
                        <label>Número de Registro <span style="color:#c0392b">*</span></label>
                        <input type="text" name="registro"
                            value="{{ old('registro') }}"
                            class="{{ $errors->has('registro') ? 'has-error' : '' }}"
                            placeholder="Ej: REG-SIS01"
                            required />
                        @if($errors->has('registro')) <div class="field-error">{{ $errors->first('registro') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Perfil <span style="color:#c0392b">*</span></label>
                        <select name="id_perfil" id="perfil-select"
                            class="{{ $errors->has('id_perfil') ? 'has-error' : '' }}"
                            onchange="toggleRequisitos(this.value)"
                            required>
                            <option value="">-- Seleccionar Perfil --</option>
                            @foreach($perfiles as $perfil)
                            <option value="{{ $perfil->id }}"
                                data-nombre="{{ strtolower($perfil->nombre) }}"
                                {{ old('id_perfil') == $perfil->id ? 'selected' : '' }}>
                                {{ $perfil->nombre }}
                            </option>
                            @endforeach
                        </select>
                        @if($errors->has('id_perfil')) <div class="field-error">{{ $errors->first('id_perfil') }}</div> @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- CUENTA DE USUARIO --}}
        <div class="section-card">
            <div class="section-card-header">
                <span>🔐</span>
                <h3>Cuenta de Acceso</h3>
            </div>
            <div class="section-card-body">
                <div class="info-box">
                    <span class="info-box-icon">ℹ️</span>
                    <div>
                        <strong>Cuenta generada automáticamente</strong><br>
                        El nombre de usuario se generará como <em>inicial + apellido + número</em> (ej: <code>cperez42</code>).
                        La contraseña inicial será la cédula de identidad del personal. Ambos pueden cambiarse posteriormente.
                    </div>
                </div>
            </div>
        </div>

        {{-- CREDENCIALES ACADÉMICAS — solo para docentes --}}
        <div class="section-card" id="seccion-requisitos">
            <div class="section-card-header">
                <span>🎓</span>
                <h3>Credenciales Académicas</h3>
            </div>
            <div class="section-card-body">
                <div id="credenciales-container"></div>
                <button type="button" class="btn-add-row" onclick="agregarFila()">
                    + Agregar Credencial
                </button>
            </div>
        </div>

    </form>
</div>

<script>
    let contador = 0;

    function toggleRequisitos(idPerfil) {
        const select = document.getElementById('perfil-select');
        const option = select.options[select.selectedIndex];
        const nombre = option ? option.dataset.nombre : '';
        const seccion = document.getElementById('seccion-requisitos');
        seccion.style.display = nombre === 'docente' ? 'block' : 'none';
    }

    function agregarFila() {
        const container = document.getElementById('credenciales-container');
        const index = contador++;
        const fila = document.createElement('div');
        fila.className = 'credencial-row';
        fila.innerHTML = `
            <div class="form-group">
                <label>Área</label>
                <input type="text" name="credenciales[${index}][area]" placeholder="Ej: Matemáticas" />
            </div>
            <div class="form-group">
                <label>Nivel de Grado</label>
                <input type="text" name="credenciales[${index}][nivel_grado]" placeholder="Ej: Licenciatura" />
            </div>
            <div class="form-group">
                <label>Nivel de Experiencia</label>
                <input type="text" name="credenciales[${index}][nivel_exp]" placeholder="Ej: 5 años"/>
            </div>
            <div class="form-group">
                <label>Maestría <span style="color:#c0392b">*</span></label>
                <input type="text" name="credenciales[${index}][maestria]" placeholder="Ej: Maestría en Educación" required />
            </div>
            <div class="form-group">
                <label>Doctorado <span style="color:#c0392b">*</span></label>
                <input type="text" name="credenciales[${index}][doctorado]" placeholder="Ej: Doctorado en Educación" required />
            </div>
            <div class="form-group">
                <label>Diplomado <span style="color:#c0392b">*</span></label>
                <input type="text" name="credenciales[${index}][diplomado]" placeholder="Ej: Diplomado en Docencia Superior" required />
            </div>
            <button type="button" class="btn-remove-row" onclick="this.closest('.credencial-row').remove()">✕</button>
        `;
        container.appendChild(fila);
    }

    // Restaurar estado si hubo error de validación
    document.addEventListener('DOMContentLoaded', function() {
        const idPerfil = '{{ old("id_perfil") }}';
        if (idPerfil) toggleRequisitos(idPerfil);
    });
</script>
@endsection
