@extends('layouts.app')

@section('title', 'Gestión de Usuarios y Perfiles - CUP')
@section('page_title', 'Gestión de Usuarios y Perfiles')

@section('content')
<style>
    .usuarios-wrapper {
        width: 100%;
        font-family: 'Source Sans 3', sans-serif;
    }

    /* TABS */
    .tabs {
        display: inline-flex;
        gap: 0;
        margin-bottom: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .tab {
        padding: 14px 24px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        color: #5a5a5a;
        text-decoration: none;
        border-bottom: 3px solid transparent;
    }

    .tab.active {
        color: #0d3b6e;
        border-bottom-color: #0d3b6e;
        background: #f8fafc;
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

    /* CARDS & TABLES */
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
        text-transform: capitalize;
    }

    .badge-blue {
        background: #dceeff;
        color: #1a5fa8;
    }

    .badge-green {
        background: #d4f5e2;
        color: #1a7a3c;
    }

    .badge-orange {
        background: #fef3e2;
        color: #d35400;
    }

    .badge-purple { background: #d1fae5; color: #065f46; }
    .badge-cyan   { background: #cffafe; color: #036d80; }

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

    .pagination-box svg { width: 14px; height: 14px; }
    .pagination-box nav { display: flex; align-items: center; gap: 4px; }
    .pagination-box span,
    .pagination-box a { font-size: 13px; padding: 4px 10px; border-radius: 4px; color: #1a5fa8; text-decoration: none; }
    .pagination-box a:hover { background: #dceeff; }
    .pagination-box p { display: none; }
    .pagination-box nav > div:first-child { display: none; } /* ← oculta el texto duplicado */

    @media (max-width: 768px) {
        .toolbar-form { flex-wrap: wrap; }
        .search-box { width: 100%; flex: unset; }
        .filter-select { flex: 1; min-width: 0; font-size: 13px; padding: 8px 10px; }
        .table-card { overflow-x: auto; }
        .custom-table { min-width: 600px; }
        .btn-action { padding: 5px 8px; font-size: 10px; }
    }
</style>

@if(session('success'))
<div
    style="background: #d4f5e2; color: #1a7a3c; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 13.5px; font-weight: 600;">
    {{ session('success') }}
</div>
@endif

<div class="usuarios-wrapper">
    <div class="tabs">
        <a href="{{ route('usuarios.index') }}" class="tab active">👤 Usuarios</a>
        <a href="{{ route('perfiles.index') }}" class="tab">🔑 Perfiles y Privilegios</a>
    </div>

    <form action="{{ route('usuarios.index') }}" method="GET" class="toolbar-form" id="usuariosForm">
        <div class="search-box">
            <input type="text" name="filtro" placeholder="Buscar por usuario o correo..." value="{{ $filtro }}"
                autocomplete="off" />
        </div>

        <select name="id_perfil" class="filter-select" onchange="document.getElementById('usuariosForm').submit();">
            <option value="Todos los perfiles" {{ $perfil_id=='Todos los perfiles' ? 'selected' : '' }}>Todos los
                perfiles</option>
            @foreach($perfiles as $perf)
            <option value="{{ $perf->id }}" {{ $perfil_id==$perf->id ? 'selected' : '' }}>
                {{ $perf->nombre }}
            </option>
            @endforeach
        </select>
    </form>

    <div class="table-card">
        <div class="table-header">
            <h2>Listado de Usuarios</h2>
            <div style="display:flex;gap:10px;align-items:center;">
                <span class="total-badge">{{ $totalUsuarios }} usuarios registrados</span>
            </div>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Correo Electrónico</th>
                    <th>Perfil Asignado</th>
                    <th>Personal Asignado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                @php
                $badgeStyle = match(strtolower($u->perfil->nombre ?? '')) {
                    'sistema'       => 'badge-purple',
                    'administrador' => 'badge-blue',
                    'docente'       => 'badge-cyan',
                    default         => 'badge-gray',
                };
                @endphp
                <tr>
                    <td><strong>{{ $u->user_name }}</strong></td>
                    <td>{{ $u->email }}</td>
                    <td>
                        <span class="badge {{ $badgeStyle }}">
                            {{ $u->perfil->nombre ?? 'Sin Perfil' }}
                        </span>
                    </td>
                    <td>
                        @if($u->personal)
                        <strong>{{ $u->registro_personal }}</strong> ·
                        {{ $u->personal->datosPersonales->nombre ?? '' }} {{ $u->personal->datosPersonales->apellido ??
                        '' }}
                        @else
                        <span style="color: #999;">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions-cluster">
                            <a href="{{ route('usuarios.edit', $u->id) }}"
                                class="btn-action btn-edit">Editar</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px; color: #888;">
                        No se encontraron cuentas de usuario con los criterios especificados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-box">
            {{ $usuarios->links() }}
        </div>
    </div>
</div>
@endsection