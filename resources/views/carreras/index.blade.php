@extends('layouts.app')

@section('title', 'Gestión de Carreras y Cupos - CUP')
@section('page_title', 'Gestión de Carreras y Cupos')

@section('content')
<style>
    .carreras-wrapper {
        width: 100%;
        font-family: 'Source Sans 3', sans-serif;
    }

    .gestion-selector {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 18px 24px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .gestion-selector label {
        font-size: 12px;
        font-weight: 600;
        color: #0d3b6e;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        white-space: nowrap;
    }

    .filter-select {
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        background: white;
        color: #333;
    }

    .table-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .table-header {
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-header h2 {
        font-family: 'Merriweather', serif;
        color: #0d3b6e;
        font-size: 15px;
    }

    .table-scroll { overflow-x: auto; }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 700px;
    }

    .custom-table thead { background: #0d3b6e; color: white; }

    .custom-table th {
        padding: 11px 14px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .custom-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
        color: #333;
        vertical-align: middle;
    }

    .custom-table tr:last-child td { border-bottom: none; }
    .custom-table tr:hover td { background: #f8fafc; }

    .cupos-bar { display: flex; align-items: center; gap: 10px; }
    .bar-bg { flex: 1; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; min-width: 80px; }
    .bar-fill { height: 100%; border-radius: 4px; transition: width 0.4s ease; }
    .bar-green  { background: #27ae60; }
    .bar-yellow { background: #f39c12; }
    .bar-red    { background: #c0392b; }
    .bar-label  { font-size: 12px; color: #5a5a5a; white-space: nowrap; }

    .cupos-input {
        width: 70px;
        padding: 6px 10px;
        border: 1.5px solid #e2e8f0;
        border-radius: 4px;
        font-size: 13px;
        text-align: center;
        font-family: 'Source Sans 3', sans-serif;
        outline: none;
    }
    .cupos-input:focus { border-color: #2980b9; }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .badge-green      { background: #d4f5e2; color: #1a7a3c; }
    .badge-red        { background: #fde8e8; color: #c0392b; }
    .badge-yellow     { background: #fef9e7; color: #d68910; }
    .badge-presencial { background: #dbeafe; color: #1D4ED8; }
    .badge-virtual    { background: #cffafe; color: #036d80; }

    .actions { display: flex; gap: 6px; }

    .btn-action {
        padding: 5px 12px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-family: 'Source Sans 3', sans-serif;
    }

    .btn-save { background: #d4f5e2; color: #1a7a3c; }
    .btn-save:hover { background: #c2f0d5; }

    .footer-buttons {
        display: flex;
        justify-content: flex-end;
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
    }

    .btn-primary {
        padding: 11px 28px;
        border: none;
        border-radius: 6px;
        background: #0d3b6e;
        color: white;
        font-family: 'Source Sans 3', sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-primary:hover { background: #1a5fa8; }

    @media (max-width: 768px) {
        .gestion-selector { flex-wrap: wrap; }
        .table-card { overflow-x: auto; }
    }
</style>

@if(session('success'))
<div style="background:#d4f5e2; color:#1a7a3c; padding:12px; border-radius:6px; margin-bottom:15px; font-size:13.5px; font-weight:600; width:100%;">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background:#fde8e8; color:#c0392b; padding:12px; border-radius:6px; margin-bottom:15px; font-size:13.5px; font-weight:600; width:100%;">
    {{ session('error') }}
</div>
@endif

<div class="carreras-wrapper">
    <div class="gestion-selector">
        <label for="codigo_gestion_select">Gestión Académica:</label>
        <form action="{{ route('carreras.index') }}" method="GET" id="gestionForm">
            <select name="codigo_gestion" id="codigo_gestion_select" class="filter-select"
                onchange="document.getElementById('gestionForm').submit();">
                @foreach($gestiones as $g)
                <option value="{{ $g->codigo }}" {{ $gestion_seleccionada==$g->codigo ? 'selected' : '' }}>
                    {{ $g->codigo }}
                </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h2>Carreras y Cupos · Gestión {{ $gestion_seleccionada }}</h2>
        </div>

        <form action="{{ route('carreras.guardarMasivo') }}" method="POST" id="masivoForm">
            @csrf
            <input type="hidden" name="codigo_gestion" value="{{ $gestion_seleccionada }}">

            <div class="table-scroll">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Código-Plan</th>
                            <th>Carrera</th>
                            <th>Modalidad</th>
                            <th>Cupos Asignados</th>
                            <th>Inscritos</th>
                            <th>Aprobados</th>
                            <th>Reprobados</th>
                            <th>Estado de Cupos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($carreras as $c)
                        <tr>
                            <td><strong>{{ $c->codigo }}-{{ $c->plan }}</strong></td>
                            <td>{{ $c->nombre }}</td>
                            <td>
                                @if($c->modalidad === 'virtual')
                                    <span class="badge badge-virtual">Virtual</span>
                                @else
                                    <span class="badge badge-presencial">Presencial</span>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->tienePrivilegio('cupos.editar'))
                                <input type="number"
                                    name="cupos[{{ $c->codigo }}|{{ $c->plan }}|{{ $c->modalidad }}]"
                                    class="cupos-input"
                                    value="{{ $c->cupos }}" min="0" />
                                @else
                                {{ $c->cupos }}
                                @endif
                            </td>
                            <td>{{ $c->ocupados }}</td>
                            <td style="color:#27ae60; font-weight:600">{{ $c->aprobados }}</td>
                            <td style="color:#c0392b; font-weight:600">{{ $c->reprobados }}</td>
                            <td>
                                @if($c->aprobados >= $c->cupos && $c->cupos > 0)
                                    <span class="badge badge-red">Completo</span>
                                @else
                                    <span class="badge badge-green">Con vacantes</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:30px; color:#888;">
                                No existen carreras registradas para esta gestión.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(count($carreras) > 0 && Auth::user()->tienePrivilegio('cupos.editar'))
            <div class="footer-buttons">
                <button type="submit" class="btn-primary">💾 Guardar Todos los Cambios</button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection