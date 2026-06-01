@extends('layouts.app')

@section('title', 'Gestión de Usuarios - FICCT')
@section('page_title', 'Gestión de Usuarios y Perfiles')

@section('content')
<div style="width: 100%;">

    @if(session('success'))
    <div
        style="padding: 12px; background: #d4f5e2; color: #1a7a3c; border-radius: 6px; margin-bottom: 16px; font-size: 13px; font-weight: 600;">
        {{ session('success') }}
    </div>
    @endif

    <div class="tabs">
        <div class="tab active">👤 Usuarios</div>
        <div class="tab" onclick="window.location.href='{{ url('/admin/perfiles') }}'">🔑 Perfiles y Privilegios</div>
    </div>

    <form action="{{ url('/admin/usuarios') }}" method="GET" class="toolbar" id="usuarios-filter-form">
        <div class="search-box">
            <input type="text" name="filtro" placeholder="Buscar por usuario o correo..." value="{{ $filtro }}"
                onchange="document.getElementById('usuarios-filter-form').submit();" />
        </div>

        <select name="id_perfil" onchange="document.getElementById('usuarios-filter-form').submit();">
            <option value="Todos los perfiles">Todos los perfiles</option>
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
            <span class="total-badge">{{ $totalUsuarios }} usuarios</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Perfil</th>
                    <th>Personal Asignado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr>
                    <td><strong>{{ $usuario->user_name }}</strong></td>
                    <td>{{ $usuario->email }}</td>
                    <td>
                        @if(strtolower($usuario->perfil->nombre ?? '') == 'administrador')
                        <span class="badge badge-blue">Administrador</span>
                        @elseif(strtolower($usuario->perfil->nombre ?? '') == 'docente')
                        <span class="badge badge-green">Docente</span>
                        @else
                        <span class="badge badge-orange">{{ $usuario->perfil->nombre ?? 'Sin Rol' }}</span>
                        @endif
                    </td>
                    <td>
                        @if($usuario->personal)
                        {{ $usuario->registro_personal }} · {{ $usuario->personal->datosPersonales->nombre ?? '' }} {{
                        $usuario->personal->datosPersonales->apellido ?? '' }}
                        @else
                        <span style="color: #aaa;">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ url('/admin/usuarios/'.$usuario->id.'/edit') }}" class="btn-action btn-edit"
                                style="text-decoration:none;">Editar</a>
                            <a href="{{ url('/admin/usuarios/'.$usuario ->id.'/perfil') }}"
                                class="btn-action btn-perfiles" style="text-decoration:none;">Perfil</a>

                            <form action="{{ url('/admin/usuarios/'.$usuario->id) }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('¿Está seguro de eliminar físicamente esta cuenta de usuario?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--gris); padding: 20px;">No se encontraron
                        cuentas que coincidan con la búsqueda.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $usuarios->links('vendor.pagination.simple-default') }}
        </div>
    </div>
</div>
@endsection