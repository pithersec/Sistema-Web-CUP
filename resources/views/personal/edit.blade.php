@extends('layouts.app')

@section('title', 'Editar Docente - CUP')
@section('page_title', 'Editar Docente')

@section('content')
<style>
    .edit-wrapper {
        width: 100%;
        max-width: 900px;
        font-family: 'Source Sans 3', sans-serif;
    }

    .docente-header {
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

    .docente-header-left {
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

    .docente-header-info h2 {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 18px;
        margin-bottom: 4px;
    }

    .docente-header-info p { color: #5a5a5a; font-size: 13px; }

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
        justify-content: space-between;
    }

    .section-card-header h3 {
        color: white;
        font-family: 'Merriweather', serif;
        font-size: 13px;
    }

    .section-card-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
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
        align-self: end;
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

    /* BADGES */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-green { background: #d4f5e2; color: #1a7a3c; }
    .badge-red   { background: #fde8e8; color: #c0392b; }

    @media (max-width: 768px) {
        .fields-grid { grid-template-columns: repeat(2, 1fr); }
        .docente-header { flex-direction: column; align-items: flex-start; }
        .header-actions { width: 100%; justify-content: flex-end; }
        .btn-cancel, .btn-save { text-align: center; }
        .credencial-row { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 480px) {
        .fields-grid { grid-template-columns: 1fr; }
        .credencial-row { grid-template-columns: 1fr; }
    }
</style>

<div class="edit-wrapper">
    <form action="{{ route('docentes.actualizar', $docente->registro) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- HEADER --}}
        <div class="docente-header">
            <div class="docente-header-left">
                <div class="avatar-circle">
                    {{ strtoupper(substr($docente->datosPersonales->nombre ?? 'D', 0, 1)) }}
                </div>
                <div class="docente-header-info">
                    <h2>{{ $docente->datosPersonales->nombre ?? 'N/A' }} {{ $docente->datosPersonales->apellido ?? '' }}</h2>
                    <p>Registro: <strong>{{ $docente->registro }}</strong> &nbsp;·&nbsp;
                        @if($docente->estado)
                            <span class="badge badge-green">Activo</span>
                        @else
                            <span class="badge badge-red">Inactivo</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('docentes.show', $docente->registro) }}" class="btn-cancel">← Cancelar</a>
                <button type="submit" class="btn-save">💾 Guardar Cambios</button>
            </div>
        </div>

        {{-- DATOS PERSONALES --}}
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <span>👤</span>
                    <h3>Datos Personales</h3>
                </div>
            </div>
            <div class="section-card-body">
                <div class="fields-grid">
                    <div class="form-group">
                        <label>Registro</label>
                        <input type="text" value="{{ $docente->registro }}" readonly />
                        <p class="field-hint">No editable</p>
                    </div>
                    <div class="form-group">
                        <label>Cédula de Identidad</label>
                        <input type="text" value="{{ $docente->datosPersonales->ci ?? '' }}" readonly />
                        <p class="field-hint">No editable</p>
                    </div>
                    <div class="form-group">
                        <label>Nombre(s) <span style="color:#c0392b">*</span></label>
                        <input type="text" name="nombre"
                            value="{{ old('nombre', $docente->datosPersonales->nombre ?? '') }}"
                            class="{{ $errors->has('nombre') ? 'has-error' : '' }}" required />
                        @if($errors->has('nombre')) <div class="field-error">{{ $errors->first('nombre') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Apellido(s) <span style="color:#c0392b">*</span></label>
                        <input type="text" name="apellido"
                            value="{{ old('apellido', $docente->datosPersonales->apellido ?? '') }}"
                            class="{{ $errors->has('apellido') ? 'has-error' : '' }}" required />
                        @if($errors->has('apellido')) <div class="field-error">{{ $errors->first('apellido') }}</div> @endif
                    </div>
                    <div class="form-group">
                        <label>Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nac"
                            value="{{ old('fecha_nac', $docente->datosPersonales->fecha_nac ? \Carbon\Carbon::parse($docente->datosPersonales->fecha_nac)->format('Y-m-d') : '') }}" />
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono"
                            value="{{ old('telefono', $docente->datosPersonales->telefono ?? '') }}" />
                    </div>
                    <div class="form-group">
                        <label>Correo Electrónico</label>
                        <input type="email" name="correo"
                            value="{{ old('correo', $docente->datosPersonales->correo ?? '') }}" />
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" name="direccion"
                            value="{{ old('direccion', $docente->datosPersonales->direccion ?? '') }}" />
                    </div>
                </div>
            </div>
        </div>

        {{-- CREDENCIALES ACADÉMICAS --}}
        <div class="section-card">
            <div class="section-card-header">
                <div class="section-card-header-left">
                    <span>🎓</span>
                    <h3>Credenciales Académicas</h3>
                </div>
            </div>
            <div class="section-card-body">
                <div id="credenciales-container">
                    @forelse($docente->requisitosPersonal as $index => $req)
                    <div class="credencial-row" id="row-{{ $index }}">
                        <input type="hidden" name="credenciales[{{ $index }}][id]" value="{{ $req->id }}">
                        <div class="form-group">
                            <label>Área</label>
                            <input type="text" name="credenciales[{{ $index }}][area]"
                                value="{{ old("credenciales.$index.area", $req->area) }}" />
                        </div>
                        <div class="form-group">
                            <label>Nivel de Grado</label>
                            <input type="text" name="credenciales[{{ $index }}][nivel_grado]"
                                value="{{ old("credenciales.$index.nivel_grado", $req->nivel_grado) }}" />
                        </div>
                        <div class="form-group">
                            <label>Nivel de Experiencia</label>
                            <input type="text" name="credenciales[{{ $index }}][nivel_exp]"
                                value="{{ old("credenciales.$index.nivel_exp", $req->nivel_exp) }}" />
                        </div>
                        <div class="form-group">
                            <label>Maestría <span style="color:#c0392b">*</span></label>
                            <input type="text" name="credenciales[{{ $index }}][maestria]"
                                value="{{ old("credenciales.$index.maestria", $req->maestria) }}" required />
                        </div>
                        <div class="form-group">
                            <label>Doctorado <span style="color:#c0392b">*</span></label>
                            <input type="text" name="credenciales[{{ $index }}][doctorado]"
                                value="{{ old("credenciales.$index.doctorado", $req->doctorado) }}" required />
                        </div>
                        <div class="form-group">
                            <label>Diplomado <span style="color:#c0392b">*</span></label>
                            <input type="text" name="credenciales[{{ $index }}][diplomado]"
                                value="{{ old("credenciales.$index.diplomado", $req->diplomado) }}" required />
                        </div>
                        <button type="button" class="btn-remove-row" onclick="eliminarFila(this)">✕</button>
                    </div>
                    @empty
                    {{-- Sin credenciales existentes --}}
                    @endforelse
                </div>

                <button type="button" class="btn-add-row" onclick="agregarFila()">
                    + Agregar Credencial
                </button>
            </div>
        </div>

    </form>
</div>

<script>
    let contador = {{ $docente->requisitosPersonal->count() }};

    function agregarFila() {
        const container = document.getElementById('credenciales-container');
        const index = contador++;
        const fila = document.createElement('div');
        fila.className = 'credencial-row';
        fila.id = 'row-' + index;
        fila.innerHTML = `
            <input type="hidden" name="credenciales[${index}][id]" value="">
            <div class="form-group">
                <label>Área</label>
                <input type="text" name="credenciales[${index}][area]" />
            </div>
            <div class="form-group">
                <label>Nivel de Grado</label>
                <input type="text" name="credenciales[${index}][nivel_grado]" />
            </div>
            <div class="form-group">
                <label>Nivel de Experiencia</label>
                <input type="text" name="credenciales[${index}][nivel_exp]" />
            </div>
            <div class="form-group">
                <label>Maestría <span style="color:#c0392b">*</span></label>
                <input type="text" name="credenciales[${index}][maestria]" required />
            </div>
            <div class="form-group">
                <label>Doctorado <span style="color:#c0392b">*</span></label>
                <input type="text" name="credenciales[${index}][doctorado]" required />
            </div>
            <div class="form-group">
                <label>Diplomado <span style="color:#c0392b">*</span></label>
                <input type="text" name="credenciales[${index}][diplomado]" required />
            </div>
            <button type="button" class="btn-remove-row" onclick="eliminarFila(this)">✕</button>
        `;
        container.appendChild(fila);
    }

    function eliminarFila(btn) {
        btn.closest('.credencial-row').remove();
    }
</script>
@endsection
