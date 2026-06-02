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
        background: #f0f4f8;
        color: #5a5a5a;
    }

    .btn-view:hover {
        background: #e2e8f0;
    }

    .btn-edit {
        background: #dceeff;
        color: #1a5fa8;
    }

    .btn-edit:hover {
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
            <input type="text" name="buscar" placeholder="Buscar por CI, nombre o apellido..." value="{{ $buscar }}"
                autocomplete="off" />
        </div>

        <select name="estado" class="filter-select" onchange="document.getElementById('filterForm').submit();">
            <option value="Todos los estados" {{ $estado=='Todos los estados' ? 'selected' : '' }}>Todos los estados
            </option>
            <option value="Aprobado" {{ $estado=='Aprobado' ? 'selected' : '' }}>Aprobado</option>
            <option value="Reprobado" {{ $estado=='Reprobado' ? 'selected' : '' }}>Reprobado</option>
            <option value="preinscrito" {{ $estado=='preinscrito' ? 'selected' : '' }}>Preinscrito</option>
            <option value="En curso" {{ $estado=='En curso' ? 'selected' : '' }}>En curso</option>
            <option value="Baja" {{ $estado=='Baja' ? 'selected' : '' }}>Baja</option>
        </select>

        <select name="carrera" class="filter-select" onchange="document.getElementById('filterForm').submit();">
            <option value="Todas las carreras">Todas las carreras</option>
            @foreach($carreras as $c)
            <option value="{{ $c->codigo }}" {{ $carrera==$c->codigo ? 'selected' : '' }}>
                {{ $c->nombre }}
            </option>
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
                    <td><strong>{{ $p->ci }}</strong></td>
                    <td>{{ $p->datosPersonales->nombre ?? 'N/A' }} {{ $p->datosPersonales->apellido ?? 'N/A' }}</td>
                    <td>{{ $p->telefono_2 ?? ($p->datosPersonales->telefono ?? 'S/N') }}</td>
                    <td>{{ $p->procedencia }}</td>
                    <td>
                        @if($p->estado == 'Aprobado')
                        <span class="badge badge-green">Aprobado</span>
                        @elseif($p->estado == 'Reprobado')
                        <span class="badge badge-red">Reprobado</span>
                        @elseif($p->estado == 'preinscrito' || $p->estado == 'En curso')
                        <span class="badge badge-yellow">{{ $p->estado }}</span>
                        @else
                        <span class="badge badge-gray">{{ $p->estado }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions-cluster">
                            <a href="#" class="btn-action btn-view"
                                style="text-decoration: none;">Ver</a>
                            <a href="#" class="btn-action btn-edit"
                                style="text-decoration: none;">Editar</a>

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
                    <td colspan="6" style="text-align: center; padding: 30px; color: #888;">
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