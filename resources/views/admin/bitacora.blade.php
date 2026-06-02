@extends('layouts.app')

@section('title', 'Bitácora de Auditoría - CUP')
@section('page_title', 'Bitácora de Auditoría')

@section('content')
<style>
    .bitacora-wrapper {
        width: 100%;
        font-family: 'Source Sans 3', sans-serif;
    }

    /* FILTROS CARD */
    .filters-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        padding: 18px 24px;
        margin-bottom: 20px;
    }

    .filters-card h3 {
        font-size: 12px;
        font-weight: 600;
        color: #0d3b6e;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin-bottom: 14px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 14px;
        align-items: end;
    }

    label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #0d3b6e;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        margin-bottom: 6px;
    }

    input,
    select {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        outline: none;
        background: white;
        color: #333;
        font-family: 'Source Sans 3', sans-serif;
    }

    .btn-filtrar {
        padding: 10px 20px;
        background: #1a5fa8;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        font-family: 'Source Sans 3', sans-serif;
    }

    .btn-filtrar:hover {
        background: #0d3b6e;
    }

    /* TABLA CARD */
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
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        color: #333;
    }

    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .custom-table tr:hover td {
        background: #f8fafc;
    }

    /* BADGES DINÁMICOS */
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11.5px;
        font-weight: 600;
    }

    .badge-login {
        background: #d4f5e2;
        color: #1a7a3c;
    }

    /* Verde */
    .badge-logout {
        background: #fde8e8;
        color: #c0392b;
    }

    /* Rojo */
    .badge-create {
        background: #dceeff;
        color: #1a5fa8;
    }

    /* Azul */
    .badge-update {
        background: #fef3e2;
        color: #d35400;
    }

    /* Naranja */

    .ip-tag {
        font-family: monospace;
        font-size: 12px;
        color: #5a5a5a;
        background: #f0f4f8;
        padding: 2px 8px;
        border-radius: 4px;
        display: inline-block;
    }

    .pagination-box {
        padding: 14px 24px;
        border-top: 1px solid #e2e8f0;
        background: white;
    }
</style>

<div class="bitacora-wrapper">
    <div class="filters-card">
        <h3>Filtros de búsqueda</h3>
        <form action="{{ route('bitacora.index') }}" method="GET" class="filters-grid">
            <div>
                <label>Fecha</label>
                <input type="date" name="filtroFecha" value="{{ $fechaBusqueda }}" />
            </div>
            <div>
                <label>Usuario</label>
                <select name="filtroUsuario">
                    <option value="Todos los usuarios">Todos los usuarios</option>
                    @foreach($usuarios as $u)
                    <option value="{{ $u->id }}" {{ $usuarioBusqueda==$u->id ? 'selected' : '' }}>
                        {{ $u->user_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Tipo de Acción</label>
                <select name="filtroAccion">
                    <option value="Todas las acciones" {{ $tipoAccion=='Todas las acciones' ? 'selected' : '' }}>Todas
                        las acciones</option>
                    <option value="Inicio de sesión" {{ $tipoAccion=='Inicio de sesión' ? 'selected' : '' }}>Inicio de
                        sesión</option>
                    <option value="Cierre de sesión" {{ $tipoAccion=='Cierre de sesión' ? 'selected' : '' }}>Cierre de
                        sesión</option>
                    <option value="Registro" {{ $tipoAccion=='Registro' ? 'selected' : '' }}>Registro / Creación
                    </option>
                    <option value="Modificación" {{ $tipoAccion=='Modificación' ? 'selected' : '' }}>Modificación /
                        Actualización</option>
                    <option value="Eliminación" {{ $tipoAccion=='Eliminación' ? 'selected' : '' }}>Eliminación</option>
                </select>
            </div>
            <button type="submit" class="btn-filtrar">🔍 Filtrar</button>
        </form>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h2>Registro de Eventos</h2>
            <span class="total-badge">{{ number_format($totalEventos) }} eventos encontrados</span>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 180px;">Fecha y Hora</th>
                    <th style="width: 140px;">Usuario</th>
                    <th>Acción Realizada</th>
                    <th style="width: 140px;">Dirección IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eventos as $e)
                @php
                // Mapeo dinámico de estilos según la descripción textual del campo accion
                $badgeClass = 'badge-create';
                $text = strtolower($e->accion);

                if (str_contains($text, 'inicio de sesión')) {
                $badgeClass = 'badge-login';
                } elseif (str_contains($text, 'cierre de sesión')) {
                $badgeClass = 'badge-logout';
                } elseif (str_contains($text, 'modificación') || str_contains($text, 'actualiz')) {
                $badgeClass = 'badge-update';
                } elseif (str_contains($text, 'elimin')) {
                $badgeClass = 'badge-logout';
                }
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($e->fecha_hora)->format('d/m/Y H:i:s') }}</td>
                    <td><strong>{{ $e->usuario->user_name ?? 'Sistema / Externo' }}</strong></td>
                    <td>
                        <span class="badge {{ $badgeClass }}">
                            {{ $e->accion }}
                        </span>
                    </td>
                    <td><span class="ip-tag">{{ $e->ip ?? '127.0.0.1' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px; color: #888;">
                        No existen registros de auditoría que coincidan con los filtros aplicados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-box">
            {{ $eventos->links() }}
        </div>
    </div>
</div>
@endsection