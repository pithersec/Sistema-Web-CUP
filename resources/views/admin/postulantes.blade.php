@extends('layouts.app')

@section('title', 'Gestión de Postulantes - FICCT')
@section('page_title', 'Gestión de Postulantes')

@section('content')
<div style="width: 100%;">

    <form action="{{ url('/admin/postulantes') }}" method="GET" class="toolbar" id="filter-form">
        <div class="search-box">
            <input type="text" name="buscar" placeholder="Buscar por CI, nombre o apellido..." value="{{ $buscar }}"
                onchange="document.getElementById('filter-form').submit();" />
        </div>

        <select name="estado" onchange="document.getElementById('filter-form').submit();">
            <option value="Todos los estados" {{ $estado=='Todos los estados' ? 'selected' : '' }}>Todos los estados
            </option>
            <option value="Aprobado" {{ $estado=='Aprobado' ? 'selected' : '' }}>Aprobado</option>
            <option value="Reprobado" {{ $estado=='Reprobado' ? 'selected' : '' }}>Reprobado</option>
            <option value="preinscrito" {{ $estado=='preinscrito' ? 'selected' : '' }}>En curso</option>
            <option value="Baja" {{ $estado=='Baja' ? 'selected' : '' }}>Baja</option>
        </select>

        <select name="carrera" onchange="document.getElementById('filter-form').submit();">
            <option value="Todas las carreras">Todas las carreras</option>
            @foreach($carreras as $c)
            <option value="{{ $c->codigo }}" {{ $carrera==$c->codigo ? 'selected' : '' }}>{{ $c->nombre }}</option>
            @endforeach
        </select>
    </form>

    <div class="table-card">
        <div class="table-header">
            <h2>Listado de Postulantes</h2>
            <span class="total-badge">{{ $totalPostulantes }} postulantes</span>
        </div>

        <table>
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
                @forelse($postulantes as $postulante)
                <tr>
                    <td>{{ $postulante->ci }}</td>
                    <td>{{ $postulante->nombre }} {{ $postulante->apellido }}</td>
                    <td>{{ $postulante->telefono ?? 'S/N' }}</td>
                    <td>{{ $postulante->procedencia }}</td>
                    <td>
                        @if(strtolower($postulante->estado) == 'aprobado')
                        <span class="badge badge-green">Aprobado</span>
                        @elseif(strtolower($postulante->estado) == 'reprobado')
                        <span class="badge badge-red">Reprobado</span>
                        @elseif(strtolower($postulante->estado) == 'baja')
                        <span class="badge badge-gray">Baja</span>
                        @else
                        <span class="badge badge-yellow">En curso</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ url('/admin/postulantes/'.$postulante->codigo) }}" class="btn-action btn-view"
                                style="text-decoration:none;">Ver</a>
                            <a href="{{ url('/admin/postulantes/'.$postulante->codigo.'/edit') }}"
                                class="btn-action btn-edit" style="text-decoration:none;">Editar</a>

                            <form action="{{ url('/admin/postulantes/'.$postulante->codigo.'/baja') }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('¿Está seguro de dar de baja a este postulante?');">
                                @csrf
                                <button type="submit" class="btn-action btn-delete">Baja</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--gris); padding: 20px;">No se encontraron
                        postulantes con los criterios especificados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $postulantes->links('vendor.pagination.simple-default') }}
        </div>
    </div>
</div>
@endsection