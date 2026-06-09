@extends('layouts.app')

@section('title', 'Ver Postulante - CUP')
@section('page_title', 'Detalle de Postulante')

@section('content')
<style>
    .show-wrapper {
        width: 100%;
        max-width: 900px;
        font-family: 'Source Sans 3', sans-serif;
    }

    /* HEADER */
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

    .btn-back {
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

    .btn-back:hover { background: #f8fafc; }

    .btn-edit-header {
        padding: 9px 18px;
        border: none;
        border-radius: 6px;
        background: #dceeff;
        color: #1a5fa8;
        font-family: 'Source Sans 3', sans-serif;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-edit-header:hover { background: #cce3ff; }

    /* SECCIONES */
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

    .section-card-body {
        padding: 20px;
    }

    /* GRID DE CAMPOS */
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

    .field-item label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #5a5a5a;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin-bottom: 4px;
    }

    .field-item p {
        font-size: 14px;
        color: #1a1a1a;
        font-weight: 500;
    }

    .field-item p.empty {
        color: #aaa;
        font-style: italic;
        font-weight: 400;
    }

    /* REQUISITOS CHECKLIST */
    .requisitos-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .requisito-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #333;
    }

    .req-check {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        flex-shrink: 0;
    }

    .req-check.ok { background: #d4f5e2; color: #1a7a3c; }
    .req-check.no { background: #fde8e8; color: #c0392b; }

    /* CARRERAS */
    .carrera-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .carrera-opcion {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #0d3b6e;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .carrera-info strong {
        display: block;
        font-size: 14px;
        color: #0d3b6e;
    }

    .carrera-info span {
        font-size: 12px;
        color: #5a5a5a;
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
    .badge-presencial { background: #dbeafe; color: #1D4ED8; }
    .badge-virtual    { background: #cffafe; color: #036d80; }

    /* PAGO */
    .pago-estado-ok  { color: #1a7a3c; font-weight: 600; }
    .pago-estado-pen { color: #d68910; font-weight: 600; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .fields-grid { grid-template-columns: repeat(2, 1fr); }
        .fields-grid-2 { grid-template-columns: 1fr; }
        .requisitos-grid { grid-template-columns: repeat(2, 1fr); }
        .postulante-header { flex-direction: column; align-items: flex-start; }
        .header-actions { width: 100%; justify-content: flex-end; }
    }

    @media (max-width: 480px) {
        .fields-grid { grid-template-columns: 1fr; }
        .requisitos-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="show-wrapper">
    @if(session('success'))
    <div style="background:#d4f5e2; color:#1a7a3c; padding:12px 16px; border-radius:6px; margin-bottom:16px; font-size:13.5px; font-weight:600;">
        ✓ {{ session('success') }}
    </div>
    @endif

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
            <a href="{{ route('postulantes.index') }}" class="btn-back">← Volver</a>
            @if(Auth::user()->tienePrivilegio('postulantes.editar'))
            <a href="{{ route('postulantes.edit', $postulante->codigo) }}" class="btn-edit-header">✏️ Editar</a>
            @endif
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
                <div class="field-item">
                    <label>Cédula de Identidad</label>
                    <p>{{ $postulante->ci }}</p>
                </div>
                <div class="field-item">
                    <label>Nombre(s)</label>
                    <p>{{ $postulante->datosPersonales->nombre ?? 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Apellido(s)</label>
                    <p>{{ $postulante->datosPersonales->apellido ?? 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Género</label>
                    <p>{{ $postulante->datosPersonales->genero == 'm' ? 'Masculino' : 'Femenino' }}</p>
                </div>
                <div class="field-item">
                    <label>Fecha de Nacimiento</label>
                    <p>{{ $postulante->datosPersonales->fecha_nac ? \Carbon\Carbon::parse($postulante->datosPersonales->fecha_nac)->format('d/m/Y') : 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Procedencia</label>
                    <p>{{ $postulante->procedencia ?? 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Teléfono</label>
                    <p>{{ $postulante->datosPersonales->telefono ?? 'S/N' }}</p>
                </div>
                <div class="field-item">
                    <label>Teléfono 2</label>
                    <p>{{ $postulante->telefono_2 ?? 'S/N' }}</p>
                </div>
                <div class="field-item">
                    <label>Correo Electrónico</label>
                    <p>{{ $postulante->datosPersonales->correo ?? 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Dirección</label>
                    <p>{{ $postulante->datosPersonales->direccion ?? 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Año de Egreso de Colegio</label>
                    <p>{{ $postulante->gestion_egreso ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- COLEGIO --}}
    <div class="section-card">
        <div class="section-card-header">
            <span>🏫</span>
            <h3>Colegio de Procedencia</h3>
        </div>
        <div class="section-card-body">
            @if($postulante->colegio)
            <div class="fields-grid">
                <div class="field-item">
                    <label>Nombre</label>
                    <p>{{ $postulante->colegio->nombre }}</p>
                </div>
                <div class="field-item">
                    <label>CIE</label>
                    <p>{{ $postulante->colegio->cie }}</p>
                </div>
                <div class="field-item">
                    <label>Tipo</label>
                    <p>{{ ucfirst($postulante->colegio->tipo) }}</p>
                </div>
                <div class="field-item">
                    <label>Turno</label>
                    <p>{{ ucfirst($postulante->colegio->turno) }}</p>
                </div>
                <div class="field-item">
                    <label>País</label>
                    <p>{{ $postulante->colegio->pais }}</p>
                </div>
                <div class="field-item">
                    <label>Departamento / Provincia</label>
                    <p>{{ $postulante->colegio->departamento }} · {{ $postulante->colegio->provincia }}</p>
                </div>
            </div>
            @else
            <p class="empty" style="color:#aaa; font-style:italic; font-size:13px;">No asignado</p>
            @endif
        </div>
    </div>

    {{-- DATOS DE REGISTRO --}}
    <div class="section-card">
        <div class="section-card-header">
            <span>📋</span>
            <h3>Datos de Registro</h3>
        </div>
        <div class="section-card-body">
            <div class="fields-grid">
                <div class="field-item">
                    <label>Grupo Asignado</label>
                    <p>{{ $grupo ? 'Grupo ' . $grupo->id . ' — ' . ucfirst($grupo->nombre_turno) : 'Sin grupo' }}</p>
                </div>
                <div class="field-item">
                    <label>Gestión</label>
                    <p>{{ $postulante->grupo?->codigo_gestion ?? 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Turno Preferido</label>
                    <p>{{ $postulante->nombre_turno ? ucfirst($postulante->nombre_turno) : 'No especificado' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- CARRERAS --}}
    <div class="section-card">
        <div class="section-card-header">
            <span>🎓</span>
            <h3>Carreras Seleccionadas</h3>
        </div>
        <div class="section-card-body">
            @forelse($carreras as $carrera)
            <div class="carrera-item">
                <div class="carrera-opcion">{{ $carrera->opcion }}</div>
                <div class="carrera-info">
                    <strong>{{ $carrera->nombre }}</strong>
                    <span>{{ $carrera->codigo }} - {{ $carrera->plan }} &nbsp;·&nbsp;
                        @if($carrera->modalidad === 'virtual')
                            <span class="badge badge-virtual">Virtual</span>
                        @else
                            <span class="badge badge-presencial">Presencial</span>
                        @endif
                    </span>
                </div>
            </div>
            @empty
            <p style="color:#aaa; font-style:italic; font-size:13px;">Sin carreras registradas</p>
            @endforelse
        </div>
    </div>

    {{-- REQUISITOS --}}
    <div class="section-card">
        <div class="section-card-header">
            <span>📄</span>
            <h3>Requisitos Entregados</h3>
        </div>
        <div class="section-card-body">
            @if($postulante->requisitosPostulante)
            @php $req = $postulante->requisitosPostulante; @endphp
            <form action="{{ route('postulantes.requisitos', $postulante->codigo) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="requisitos-grid">
                    @foreach([
                        'titulo_original'  => 'Título Original',
                        'titulo_copia'     => 'Copia del Título',
                        'fotocopia_carnet' => 'Fotocopia Carnet',
                        'formulario'       => 'Formulario',
                        'libreta'          => 'Libreta Escolar',
                    ] as $campo => $label)
                    <label style="display:flex; align-items:center; gap:10px; font-size:13px; color:#333; text-transform:none; letter-spacing:0; font-weight:500; cursor:pointer;">
                        <input type="checkbox" name="{{ $campo }}" value="1"
                            {{ $req->$campo ? 'checked' : '' }}
                            style="width:16px; height:16px; cursor:pointer; accent-color:#0d3b6e;" />
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
                <div style="display:flex; justify-content:flex-end; margin-top:16px; padding-top:14px; border-top:1px solid #e2e8f0;">
                    <button type="submit" style="padding:8px 20px; background:#0d3b6e; color:white; border:none; border-radius:6px; font-family:'Source Sans 3',sans-serif; font-size:13px; font-weight:600; cursor:pointer;">
                        💾 Guardar Requisitos
                    </button>
                </div>
            </form>
            @else
            <p style="color:#aaa; font-style:italic; font-size:13px;">Sin requisitos registrados</p>
            @endif
        </div>
    </div>

    {{-- PAGO --}}
    <div class="section-card">
        <div class="section-card-header">
            <span>💳</span>
            <h3>Información de Pago</h3>
        </div>
        <div class="section-card-body">
            @if($postulante->pago)
            @php $pago = $postulante->pago; @endphp
            <div class="fields-grid">
                <div class="field-item">
                    <label>Monto</label>
                    <p>Bs. {{ number_format($pago->monto, 2) }}</p>
                </div>
                <div class="field-item">
                    <label>Fecha</label>
                    <p>{{ $pago->fecha ? \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') : 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Estado</label>
                    <p class="{{ $pago->estado == 'completado' ? 'pago-estado-ok' : 'pago-estado-pen' }}">
                        {{ ucfirst($pago->estado) }}
                    </p>
                </div>
                <div class="field-item">
                    <label>Concepto</label>
                    <p>{{ $pago->concepto }}</p>
                </div>
                <div class="field-item">
                    <label>ID Transacción</label>
                    <p>{{ $pago->id_transaccion ?? 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Moneda</label>
                    <p>{{ $pago->moneda ?? 'USD' }}</p>
                </div>
            </div>
            @else
            <p style="color:#aaa; font-style:italic; font-size:13px;">Sin pago registrado</p>
            @endif
        </div>
    </div>

</div>
@endsection
