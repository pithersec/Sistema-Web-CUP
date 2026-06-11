@extends('layouts.app')

@section('title', 'Ver Personal - CUP')
@section('page_title', 'Detalle de Personal')

@section('content')
<style>
    .show-wrapper {
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

    .docente-header-info p {
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
        display: inline-block;
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
        display: inline-block;
        cursor: pointer;
    }

    .btn-edit-header:hover { background: #cce3ff; }

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

    /* TABLA REQUISITOS */
    .req-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .req-table thead { background: #f8fafc; }

    .req-table th {
        padding: 10px 14px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #5a5a5a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
    }

    .req-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
        color: #333;
    }

    .req-table tr:last-child td { border-bottom: none; }
    .req-table tr:hover td { background: #f8fafc; }

    .req-scroll { overflow-x: auto; }

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

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .fields-grid { grid-template-columns: repeat(2, 1fr); }
        .docente-header { flex-direction: column; align-items: flex-start; }
        .header-actions { width: 100%; justify-content: flex-end; }
        .req-scroll { overflow-x: auto; }
        .req-table { min-width: 500px; }
    }

    @media (max-width: 480px) {
        .fields-grid { grid-template-columns: 1fr; }
    }
</style>

@if(session('success'))
<div style="background:#d4f5e2; color:#1a7a3c; padding:12px 16px; border-radius:6px; margin-bottom:16px; font-size:13.5px; font-weight:600; width:100%;">
    ✓ {{ session('success') }}
</div>
@endif

<div class="show-wrapper">

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
            <a href="{{ route('personal.index') }}" class="btn-back">← Volver</a>
            @if(Auth::user()->tienePrivilegio('personal.editar'))
            <a href="{{ route('personal.edit', $docente->registro) }}" class="btn-edit-header">✏️ Editar</a>
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
                    <p>{{ $docente->datosPersonales->ci ?? 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Nombre(s)</label>
                    <p>{{ $docente->datosPersonales->nombre ?? 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Apellido(s)</label>
                    <p>{{ $docente->datosPersonales->apellido ?? 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Género</label>
                    <p>{{ $docente->datosPersonales->genero == 'm' ? 'Masculino' : ($docente->datosPersonales->genero == 'f' ? 'Femenino' : 'N/A') }}</p>
                </div>
                <div class="field-item">
                    <label>Fecha de Nacimiento</label>
                    <p>{{ $docente->datosPersonales->fecha_nac ? \Carbon\Carbon::parse($docente->datosPersonales->fecha_nac)->format('d/m/Y') : 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Teléfono</label>
                    <p>{{ $docente->datosPersonales->telefono ?? 'S/N' }}</p>
                </div>
                <div class="field-item">
                    <label>Correo Electrónico</label>
                    <p>{{ $docente->datosPersonales->correo ?? 'N/A' }}</p>
                </div>
                <div class="field-item">
                    <label>Dirección</label>
                    <p>{{ $docente->datosPersonales->direccion ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- CREDENCIALES ACADÉMICAS --}}
    <div class="section-card">
        <div class="section-card-header">
            <span>🎓</span>
            <h3>Credenciales Académicas</h3>
        </div>
        <div class="section-card-body">
            @if($docente->requisitosPersonal->count() > 0)
            <div class="req-scroll">
                <table class="req-table">
                    <thead>
                        <tr>
                            <th>Área</th>
                            <th>Nivel de Grado</th>
                            <th>Nivel de Experiencia</th>
                            <th>Maestría</th>
                            <th>Doctorado</th>
                            <th>Diplomado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($docente->requisitosPersonal as $req)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $req->area)) }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $req->nivel_grado)) }}</td>
                            <td>{{ $req->nivel_exp !== null ? $req->nivel_exp . ' años' : 'N/A' }}</td>
                            <td>{{ $req->maestria ? 'Sí' : 'No' }}</td>
                            <td>{{ $req->doctorado ? 'Sí' : 'No' }}</td>
                            <td>{{ $req->diplomado ? 'Sí' : 'No' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p style="color:#aaa; font-style:italic; font-size:13px;">Sin credenciales registradas</p>
            @endif
        </div>
    </div>

</div>
@endsection
