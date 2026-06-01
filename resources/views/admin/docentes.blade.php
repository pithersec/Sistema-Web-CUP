@extends('layouts.app')

@section('title', 'Gestión de Docentes - FICCT')
@section('page_title', 'Gestión de Docentes')

@section('content')
<div style="width: 100%;">

    @if(session('success'))
    <div
        style="padding: 12px; background: #d4f5e2; color: #1a7a3c; border-radius: 6px; margin-bottom: 16px; font-size: 13px; font-weight: 600;">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ url('/admin/docentes') }}" method="GET" class="toolbar" id="docente-form">
        <div class="search-box">
            <input type="text" name="filtro" placeholder="Buscar por CI, nombre o apellido..." value="{{ $filtro }}"
                onchange="document.getElementById('docente-form').submit();" />
        </div>

        <select name="estado" onchange="document.getElementById('docente-form').submit();">
            <option value="Todos los estados" {{ $estado=='Todos los estados' ? 'selected' : '' }}>Todos los estados
            </option>
            <option value="Activo" {{ $estado=='Activo' ? 'selected' : '' }}>Activo</option>
            <option value="Inactivo" {{ $estado=='Inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
    </form>

    <div class="table-card">
        <div class="table-header">
            <h2>Listado de Docentes</h2>
            <span class="total-badge">{{ $totalDocentes }} docentes</span>
        </div>

        <table>
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
                @forelse($docentes as $docente)
                <tr>
                    <td><strong>{{ $docente->registro }}</strong></td>
                    <td>{{ $docente->datosPersonales->ci ?? 'S/R' }}</td>
                    <td>{{ $docente->datosPersonales->nombre ?? '' }} {{ $docente->datosPersonales->apellido ?? '' }}
                    </td>
                    <td>{{ $docente->datosPersonales->correo ?? 'S/C' }}</td>
                    <td>{{ $docente->datosPersonales->telefono ?? 'S/N' }}</td>
                    <td>
                        @if(strtolower($docente->estado) == 'activo')
                        <span class="badge badge-green">Activo</span>
                        @else
                        <span class="badge badge-gray">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ url('/admin/docentes/'.$docente->registro) }}" class="btn-action btn-view"
                                style="text-decoration:none;">Ver</a>
                            <a href="{{ url('/admin/docentes/'.$docente->registro.'/edit') }}"
                                class="btn-action btn-edit" style="text-decoration:none;">Editar</a>

                            <form action="{{ url('/admin/docentes/'.$docente->registro.'/baja') }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('¿Está seguro de inactivar a este docente?');">
                                @csrf
                                <button type="submit" class="btn-action btn-delete">Baja</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--gris); padding: 20px;">No se encontraron
                        docentes con los criterios de búsqueda.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $docentes->links('vendor.pagination.simple-default') }}
        </div>
    </div>
</div>
@endsection