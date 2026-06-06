@extends('layouts.app')

@section('title', 'Gestión de Personal - CUP')
@section('page_title', 'Gestión de Personal')

@section('content')
<style>
    .docentes-wrapper {
        width: 100%;
        font-family: 'Source Sans 3', sans-serif;
    }

    /* TOOLBAR */
    .toolbar-form {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        align-items: center;
        width: 100%;
    }

    .search-box {
        flex: 1;
        position: relative;
    }

    .search-box input {
        width: 100%;
        padding: 10px 14px 10px 36px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        background: white;
    }

    .search-box::before {
        content: '🔍';
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
    }

    .filter-select {
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        background: white;
        color: #333;
        min-width: 180px;
        cursor: pointer;
    }

    /* CARD & TABLE */
    .table-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
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

    .total-badge {
        background: #dceeff;
        color: #1a5fa8;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .custom-table thead {
        background: #0d3b6e;
        color: white;
    }

    .custom-table th {
        padding: 11px 14px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    .custom-table td {
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
        color: #333;
        vertical-align: middle;
    }

    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .custom-table tr:hover td {
        background: #f8fafc;
    }

    /* BADGES */
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-green {
        background: #d4f5e2;
        color: #1a7a3c;
    }

    .badge-gray {
        background: #fde8e8;
        color: #c0392b;
    }

    /* ACTIONS */
    .actions-cluster {
        display: flex;
        gap: 6px;
    }

    .btn-action {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-family: 'Source Sans 3', sans-serif;
        text-decoration: none;
        display: inline-block;
    }

    .btn-view {
        background: #dceeff;
        color: #1a5fa8;
    }

    .btn-view:hover {
        background: #cce3ff;
    }

    .btn-delete {
        background: #fde8e8;
        color: #c0392b;
    }

    .btn-delete:hover {
        background: #fbcbcb;
    }

    .btn-desactivar {
        background: #fde8e8;
        color: #c0392b;
    }

    .btn-desactivar:hover { background: #fbcbcb; }

    .btn-activar {
        background: #d4f5e2;
        color: #1a7a3c;
    }
    .btn-activar:hover { background: #b7f0d0; }

    /* PAGINACIÓN */
    .pagination-box {
        padding: 14px 24px;
        border-top: 1px solid #e2e8f0;
        background: white;
    }

    .pagination-box svg { width: 14px; height: 14px; }
    .pagination-box nav { display: flex; align-items: center; gap: 4px; }
    .pagination-box span,
    .pagination-box a { font-size: 13px; padding: 4px 10px; border-radius: 4px; color: #1a5fa8; text-decoration: none; }
    .pagination-box a:hover { background: #dceeff; }
    .pagination-box p { display: none; }

    @media (max-width: 768px) {
        .toolbar-form { flex-wrap: wrap; }
        .search-box { width: 100%; flex: unset; }
        .filter-select { flex: 1; min-width: 0; font-size: 13px; padding: 8px 10px; }
        .table-card { overflow-x: auto; }
        .custom-table { min-width: 500px; }
        .btn-action { padding: 5px 8px; font-size: 10px; }
    }
</style>

@if(session('success'))
<div style="background: #d4f5e2; color: #1a7a3c; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 13.5px; font-weight: 600; width: 100%;">
    {{ session('success') }}
</div>
@endif

@if($errors->has('error'))
<div
    style="background: #fde8e8; color: #c0392b; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 13.5px; font-weight: 600;">
    {{ $errors->first('error') }}
</div>
@endif

<div class="docentes-wrapper">
    <form action="{{ route('personal.index') }}" method="GET" class="toolbar-form" id="docentesForm">
        <div class="search-box">
            <input type="text" name="filtro" placeholder="Buscar personal..."
                value="{{ $filtro }}" autocomplete="off" />
        </div>

        <select name="estado" class="filter-select" onchange="document.getElementById('docentesForm').submit();">
            <option value="Todos los estados" {{ $estado=='Todos los estados' ? 'selected' : '' }}>Todos los estados
            </option>
            <option value="1" {{ $estado=='1' ? 'selected' : '' }}>Activo</option>
            <option value="0" {{ $estado=='0' ? 'selected' : '' }}>Inactivo</option>
        </select>

        <select name="perfil" class="filter-select" onchange="document.getElementById('docentesForm').submit();">
            <option value="Todos los perfiles">Todos los perfiles</option>
            @foreach($perfiles as $p)
            <option value="{{ $p->id }}" {{ $perfil == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
            @endforeach
        </select>
    </form>

    <div class="table-card">
        <div class="table-header">
            <h2>
                Listado de Personal
                @if($perfil !== 'Todos los perfiles')
                    — {{ $perfiles->firstWhere('id', $perfil)?->nombre ?? '' }}
                @endif
            </h2>
            <div style="display: flex; align-items: center; gap: 10px; margin-left: auto;">
                <span class="total-badge">
                    {{ $totalDocentes }} 
                    @if($perfil !== 'Todos los perfiles')
                        {{ strtolower($perfiles->firstWhere('id', $perfil)?->nombre ?? 'registros') }}
                    @else
                        personal
                    @endif
                    encontrados
                </span>
                <a href="{{ route('personal.crear') }}" style="padding: 8px 16px; background: #0d3b6e; color: white; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none;">+ Registrar Personal</a>
            </div>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th>Registro</th>
                    <th>CI</th>
                    <th>Nombre Completo</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Perfil Asignado</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($docentes as $d)
                <tr>
                    <td><strong>{{ $d->registro }}</strong></td>
                    <td>{{ $d->datosPersonales->ci ?? 'S/CI' }}</td>
                    <td>{{ $d->datosPersonales->nombre ?? '' }} {{ $d->datosPersonales->apellido ?? '' }}</td>
                    <td>{{ $d->datosPersonales->correo ?? 'Sin Correo' }}</td>
                    <td>{{ $d->datosPersonales->telefono ?? 'S/N' }}</td>
                    <td>
                        @php
                            $badgeEstilo = match(strtolower($d->perfil_nombre ?? '')) {
                                'sistema'       => 'background:#d1fae5; color:#065f46;',
                                'administrador' => 'background:#dceeff; color:#1a5fa8;',
                                'docente'       => 'background:#cffafe; color:#036d80;',
                                default         => 'background:#f1f5f9; color:#5a5a5a;',
                            };
                        @endphp
                        <span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:600; {{ $badgeEstilo }}">
                            {{ $d->perfil_nombre ?? 'S/P' }}
                        </span>
                    </td>
                    <td>
                        @if($d->estado)
                        <span class="badge badge-green">Activo</span>
                        @else
                        <span class="badge badge-gray">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions-cluster">
                            <a href="{{ route('personal.show', $d->registro) }}" class="btn-action btn-view">Ver</a>

                            @if($d->estado)
                                <form action="{{ route('personal.desactivar', $d->registro) }}" method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('¿Desactivar al personal {{ e($d->registro) }}?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-action btn-desactivar">Desactivar</button>
                                </form>
                            @else
                                <form action="{{ route('personal.activar', $d->registro) }}" method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('¿Activar al personal {{ e($d->registro) }}?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-action btn-activar">Activar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #888;">
                        No se encontraron registros de personal bajo los criterios seleccionados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-box">
            {{ $docentes->links() }}
        </div>
    </div>
</div>
@endsection