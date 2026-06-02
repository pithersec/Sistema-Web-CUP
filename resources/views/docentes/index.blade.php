@extends('layouts.app')

@section('title', 'Gestión de Docentes - CUP')
@section('page_title', 'Gestión de Docentes')

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
        background: #e2e8f0;
        color: #5a5a5a;
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

    /* PAGINACIÓN */
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

@if($errors->has('error'))
<div
    style="background: #fde8e8; color: #c0392b; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 13.5px; font-weight: 600;">
    {{ $errors->first('error') }}
</div>
@endif

<div class="docentes-wrapper">
    <form action="{{ route('docentes.index') }}" method="GET" class="toolbar-form" id="docentesForm">
        <div class="search-box">
            <input type="text" name="filtro" placeholder="Buscar por CI, registro, nombre o apellido..."
                value="{{ $filtro }}" autocomplete="off" />
        </div>

        <select name="estado" class="filter-select" onchange="document.getElementById('docentesForm').submit();">
            <option value="Todos los estados" {{ $estado=='Todos los estados' ? 'selected' : '' }}>Todos los estados
            </option>
            <option value="Activo" {{ $estado=='Activo' ? 'selected' : '' }}>Activo</option>
            <option value="Inactivo" {{ $estado=='Inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
    </form>

    <div class="table-card">
        <div class="table-header">
            <h2>Listado de Docentes</h2>
            <span class="total-badge">{{ $totalDocentes }} docentes encontrados</span>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th>Registro</th>
                    <th>CI</th>
                    <th>Nombre Completo</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
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
                        @if(strtolower($d->estado) == 'activo')
                        <span class="badge badge-green">Activo</span>
                        @else
                        <span class="badge badge-gray">{{ $d->estado }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions-cluster">
                            <a href="{{ url('/admin/docentes/'.$d->registro) }}" class="btn-action btn-view">Ver</a>
                            <a href="{{ url('/admin/docentes/'.$d->registro.'/edit') }}"
                                class="btn-action btn-edit">Editar</a>

                            <form action="{{ route('docentes.desactivar', $d->registro) }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('¿Está seguro de cambiar a Inactivo al docente con registro {{ $d->registro }}?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-action btn-delete">Baja</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #888;">
                        No se encontraron registros de docentes bajo los criterios seleccionados.
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