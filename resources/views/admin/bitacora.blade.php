@extends('layouts.app')

@section('title', 'Bitácora de Auditoría - FICCT')
@section('page_title', 'Bitácora de Auditoría')

@section('content')
<div style="width: 100%;">

    <div class="filters-card">
        <h3>Filtros de búsqueda</h3>
        <form action="{{ url('/admin/bitacora') }}" method="GET">
            <div class="filters-grid">

                <div>
                    <label for="filtroFecha">Fecha</label>
                    <input type="date" id="filtroFecha" name="filtroFecha" value="{{ $fechaBusqueda }}" />
                </div>

                <div>
                    <label for="filtroUsuario">Usuario</label>
                    <select id="filtroUsuario" name="filtroUsuario">
                        <option value="Todos los usuarios">Todos los usuarios</option>
                        @foreach($usuarios as $user)
                        <option value="{{ $user->id }}" {{ $usuarioBusqueda==$user->id ? 'selected' : '' }}>
                            {{ $user->user_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filtroAccion">Tipo de Acción</label>
                    <select id="filtroAccion" name="filtroAccion">
                        <option value="Todas las acciones" {{ $tipoAccion=='Todas las acciones' ? 'selected' : '' }}>
                            Todas las acciones</option>
                        <option value="Inicio de sesión" {{ $tipoAccion=='Inicio de sesión' ? 'selected' : '' }}>Inicio
                            de sesión</option>
                        <option value="Cierre de sesión" {{ $tipoAccion=='Cierre de sesión' ? 'selected' : '' }}>Cierre
                            de sesión</option>
                        <option value="Registro" {{ $tipoAccion=='Registro' ? 'selected' : '' }}>Registro / Creación
                        </option>
                        <option value="Modificación" {{ $tipoAccion=='Modificación' ? 'selected' : '' }}>Modificación /
                            Actualización</option>
                        <option value="Eliminación" {{ $tipoAccion=='Eliminación' ? 'selected' : '' }}>Eliminación
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn-filtrar">🔍 Filtrar</button>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h2>Registro de Eventos</h2>
            <span class="total-badge">{{ number_format($totalEventos) }} eventos</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eventos as $ev)
                @php
                // Lógica analítica de colores basada en palabras clave
                $textoAccion = $ev->accion;
                $claseBadge = 'badge-blue'; // Por defecto

                if (str_contains(strtolower($textoAccion), 'inicio')) {
                $claseBadge = 'badge-green';
                } elseif (str_contains(strtolower($textoAccion), 'cierre') || str_contains(strtolower($textoAccion),
                'elimin')) {
                $claseBadge = 'badge-red';
                } elseif (str_contains(strtolower($textoAccion), 'modific') || str_contains(strtolower($textoAccion),
                'actualiz')) {
                $claseBadge = 'badge-orange';
                }
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($ev->fecha_hora)->format('d/m/Y H:i:s') }}</td>
                    <td><strong>{{ $ev->usuario->user_name ?? 'Sistema' }}</strong></td>
                    <td>
                        <span class="badge {{ $claseBadge }}">
                            {{ $ev->accion }}
                        </span>
                    </td>
                    <td><span class="ip-tag">{{ $ev->ip ?? '127.0.0.1' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: var(--gris); padding: 24px;">
                        No se encontraron registros de auditoría bajo las condiciones especificadas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $eventos->appends(request()->query())->links('vendor.pagination.simple-default') }}
        </div>
    </div>
</div>
@endsection