@extends('layouts.app')

@section('title', 'Gestión de Postulantes - CUP')
@section('page_title', 'Gestión de Postulantes')

@section('content')
<style>
    .postulantes-wrapper {
        width: 100%;
        font-family: 'Source Sans 3', sans-serif;
    }

    /* TOOLBAR DE BÚSQUEDA Y FILTROS */
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

    /* CONTENEDOR DE TABLA */
    .table-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
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
        font-weight: 700;
    }

    .total-badge {
        background: #dceeff;
        color: #1a5fa8;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    /* COMPONENTES DE TABLA */
    .custom-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }

    .custom-table thead {
        background: #0d3b6e;
        color: white;
    }

    .custom-table th {
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    .custom-table td {
        padding: 12px 16px;
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

    /* BADGES DE ESTADO */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-green {
        background: #d4f5e2;
        color: #1a7a3c;
    }

    .badge-red {
        background: #fde8e8;
        color: #c0392b;
    }

    .badge-yellow {
        background: #fef9e7;
        color: #d68910;
    }

    .badge-gray {
        background: #e2e8f0;
        color: #5a5a5a;
    }

    .badge-dark { 
        background: #e2e2e2; 
        color: #1a1a1a; 
    }

    .badge-blue { 
        background: #dceeff; 
        color: #1a5fa8; 
    }

    /* BOTONES DE ACCIÓN */
    .actions-cluster {
        display: flex;
        gap: 6px;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-family: 'Source Sans 3', sans-serif;
        transition: background 0.2s;
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

    /* PAGINACIÓN ADAPTADA */
    .pagination-box {
        padding: 14px 24px;
        border-top: 1px solid #e2e8f0;
        background: white;
    }

    .pagination-box svg {
        width: 14px;
        height: 14px;
    }

    .pagination-box nav {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .pagination-box span,
    .pagination-box a {
        font-size: 13px;
        padding: 4px 10px;
        border-radius: 4px;
        color: #1a5fa8;
        text-decoration: none;
    }

    .pagination-box a:hover {
        background: #dceeff;
    }

    .pagination-box p {
        display: none;  /* ← oculta el "Showing X to Y of Z results" */
    }

    .pagination-box nav > div:first-child {
        display: none;  /* ← oculta el texto duplicado */
    }

    @media (max-width: 768px) {
        .toolbar-form {
            flex-wrap: wrap;
        }

        .search-box {
            width: 100%;
            flex: unset;
        }

        .filter-select {
            flex: 1;
            min-width: 0;
            font-size: 13px;
            padding: 8px 10px;
        }

        .table-card {
            overflow-x: auto;
        }

        .custom-table {
            min-width: 600px;
        }

        .btn-action {
            padding: 5px 8px;
            font-size: 10px;
        }

        .table-header {
            padding: 12px 16px;
            flex-wrap: wrap;
            gap: 8px;
        }
    }
</style>

@if(session('success'))
<div
    style="background: #d4f5e2; color: #1a7a3c; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 13.5px; font-weight: 600;">
    {{ session('success') }}
</div>
@endif

<div class="postulantes-wrapper">
    <form action="{{ route('postulantes.index') }}" method="GET" class="toolbar-form" id="filterForm">
        <div class="search-box">
            <input type="text" name="buscar" placeholder="Buscar postulante..." value="{{ $buscar }}"
                autocomplete="off" />
        </div>

        <select name="gestion" class="filter-select" onchange="document.getElementById('filterForm').submit();">
            @foreach($gestiones as $g)
                <option value="{{ $g->codigo }}" {{ $g->codigo == $gestionCodigo ? 'selected' : '' }}>
                    Gestión {{ $g->codigo }}
                </option>
            @endforeach
        </select>

        <select name="carrera" class="filter-select" onchange="document.getElementById('filterForm').submit();">
            <option value="Todas las carreras">Todas las carreras</option>
            @foreach($carreras as $c)
            <option value="{{ $c->codigo }}|{{ $c->plan }}|{{ $c->modalidad }}" 
                {{ $carrera == $c->codigo.'|'.$c->plan.'|'.$c->modalidad ? 'selected' : '' }}>
                {{ $c->nombre }} {{ $c->modalidad === 'virtual' ? '(Virtual)' : '' }}
            </option>
            @endforeach
        </select>

        <select name="estado" class="filter-select" onchange="document.getElementById('filterForm').submit();">
            <option value="Todos los estados" {{ $estado=='Todos los estados' ? 'selected' : '' }}>Todos los estados</option>
            <option value="aprobado" {{ $estado=='aprobado' ? 'selected' : '' }}>Aprobado</option>
            <option value="reprobado" {{ $estado=='reprobado' ? 'selected' : '' }}>Reprobado</option>
            <option value="inscrito" {{ $estado=='inscrito' ? 'selected' : '' }}>Inscrito</option>
            <option value="preinscrito" {{ $estado=='preinscrito' ? 'selected' : '' }}>Preinscrito</option>
            <option value="baja" {{ $estado=='baja' ? 'selected' : '' }}>Baja</option>
        </select>

        <select name="procedencia" class="filter-select" onchange="document.getElementById('filterForm').submit();">
            <option value="Todas" {{ $procedencia=='Todas' ? 'selected' : '' }}>Todas las procedencias</option>
            @foreach(['Santa Cruz','La Paz','Cochabamba','Oruro','Potosí','Tarija','Beni','Pando','Chuquisaca','Extranjero'] as $dep)
            <option value="{{ $dep }}" {{ $procedencia==$dep ? 'selected' : '' }}>{{ $dep }}</option>
            @endforeach
        </select>
    </form>

    <div class="table-card">
        <div class="table-header">
            <h2>Listado de Postulantes</h2>
            <span class="total-badge">{{ $totalPostulantes }} postulantes encontrados</span>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>CI</th>
                    <th>Nombre Completo</th>
                    <th>Teléfono</th>
                    <th>Procedencia</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($postulantes as $p)
                <tr>
                    <td><strong>{{ $p->codigo }}</strong></td>
                    <td><strong>{{ $p->ci }}</strong></td>
                    <td>{{ $p->nombre ?? 'N/A' }} {{ $p->apellido ?? 'N/A' }}</td>
                    <td>{{ $p->telefono_2 ?? $p->telefono ?? 'S/N' }}</td>
                    <td>{{ $p->procedencia }}</td>
                    <td>
                        @if($p->estado == 'aprobado')
                            <span class="badge badge-green">Aprobado</span>
                        @elseif($p->estado == 'reprobado')
                            <span class="badge badge-red">Reprobado</span>
                        @elseif($p->estado == 'inscrito')
                            <span class="badge badge-blue">Inscrito</span>
                        @elseif($p->estado == 'preinscrito')
                            <span class="badge badge-yellow">Preinscrito</span>
                        @elseif($p->estado == 'baja')
                            <span class="badge badge-dark">Baja</span>
                        @else
                            <span class="badge badge-gray">{{ $p->estado }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions-cluster">
                            <a href="{{ route('postulantes.show', $p->codigo) }}" class="btn-action btn-view"
                                style="text-decoration: none;">Ver</a>

                            <form action="{{ route('postulantes.baja', $p->codigo) }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('¿Está seguro de dar de baja al postulante con código {{ e($p->codigo) }}?');">
                                @csrf
                                <button type="submit" class="btn-action btn-delete">Baja</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #888;">
                        No se encontraron postulantes con los criterios de búsqueda seleccionados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-box">
            {{ $postulantes->links() }}
        </div>
    </div>
</div>
@endsection