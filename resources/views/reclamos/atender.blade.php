@extends('layouts.app')

@section('title', 'Bandeja de Reclamos - CUP')
@section('page_title', 'Bandeja de Reclamos')

@section('content')
<style>
    .reclamos-wrapper { width: 100%; font-family: 'Source Sans 3', sans-serif; }

    .toolbar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 20px;
        gap: 12px;
    }
    .filter-group { display: flex; align-items: center; gap: 10px; font-size: 12px; font-weight: 600; color: #555; }
    .filter-select {
        padding: 8px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 13px;
        font-family: 'Source Sans 3', sans-serif;
        background: white;
        color: #333;
        cursor: pointer;
        outline: none;
    }
    .filter-select:focus { border-color: var(--azul-claro); }

    .table-card { background: white; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); overflow: hidden; }
    .table-header {
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-header h2 { font-family: 'Merriweather', serif; color: var(--azul); font-size: 15px; font-weight: 700; }
    .total-badge { background: #dceeff; color: #1a5fa8; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }

    .custom-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .custom-table thead { background: var(--azul); color: white; }
    .custom-table th { padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px; letter-spacing: 0.5px; }
    .custom-table td { padding: 12px 16px; border-bottom: 1px solid #e2e8f0; color: #333; vertical-align: middle; }
    .custom-table tr:last-child td { border-bottom: none; }
    .custom-table tr:hover td { background: #f8fafc; }

    .reg-cell { font-weight: 700; color: #aaa; font-size: 12px; }
    .codigo-cell { font-weight: 700; color: #333; }
    .fecha-cell { font-size: 11px; color: #888; margin-top: 2px; }
    .dirigido-cell { font-size: 12px; font-weight: 600; color: var(--azul); margin-bottom: 3px; }
    .desc-cell { font-size: 12px; color: #555; }

    .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .badge-pendiente { background: #fef9e7; color: #d68910; }
    .badge-atendido  { background: #d4f5e2; color: #1a7a3c; }
    .badge-rechazado { background: #fde8e8; color: #c0392b; }

    .btn-evaluar {
        padding: 6px 14px; background: var(--azul); color: white; border: none;
        border-radius: 6px; font-size: 11px; font-weight: 600;
        font-family: 'Source Sans 3', sans-serif; cursor: pointer; transition: background .15s;
    }
    .btn-evaluar:hover { background: var(--azul-claro); }

    .pagination-box { padding: 14px 24px; border-top: 1px solid #e2e8f0; }
    .pagination-box p { display: none; }
    .pagination-box nav > div:first-child { display: none; }
    .pagination-box nav { display: flex; align-items: center; gap: 4px; }
    .pagination-box span, .pagination-box a { font-size: 13px; padding: 4px 10px; border-radius: 4px; color: #1a5fa8; text-decoration: none; }
    .pagination-box a:hover { background: #dceeff; }
    .pagination-box svg {
        width: 14px;
        height: 14px;
    }

    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 999; align-items: center; justify-content: center; }
    .modal-overlay.active { display: flex; }
    .modal-box { background: white; border-radius: 10px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); width: 100%; max-width: 400px; overflow: hidden; }
    .modal-header { padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
    .modal-header h3 { font-family: 'Merriweather', serif; color: var(--azul); font-size: 15px; margin-bottom: 2px; }
    .modal-header p { font-size: 12px; color: #888; }
    .modal-body { padding: 20px; }
    .modal-label { font-size: 11px; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: block; }
    .modal-select { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 13px; font-family: 'Source Sans 3', sans-serif; background: white; color: #333; outline: none; }
    .modal-select:focus { border-color: var(--azul-claro); }
    .modal-footer { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-top: 1px solid #e2e8f0; }
    .btn-cancelar { font-size: 12px; font-weight: 600; color: #888; background: none; border: none; cursor: pointer; font-family: 'Source Sans 3', sans-serif; }
    .btn-cancelar:hover { color: #555; }
    .btn-aplicar { padding: 8px 20px; background: var(--azul); color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; font-family: 'Source Sans 3', sans-serif; cursor: pointer; transition: background .15s; }
    .btn-aplicar:hover { background: var(--azul-claro); }
</style>

<div class="reclamos-wrapper">

    @if(session('success'))
    <div style="background:#d4f5e2;color:#1a7a3c;padding:12px;border-radius:6px;margin-bottom:16px;font-size:13px;font-weight:600;">
        ✅ {{ session('success') }}
    </div>
    @endif

    <div class="toolbar">
        <form method="GET" action="{{ url('/admin/reclamos') }}" class="filter-group">
            <span>Estado:</span>
            <select name="filtroEstado" class="filter-select" onchange="this.form.submit()">
                <option value="Todos"     {{ ($estadoFiltro ?? '') === 'Todos'     ? 'selected' : '' }}>Todos</option>
                <option value="pendiente" {{ ($estadoFiltro ?? '') === 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                <option value="atendido"  {{ ($estadoFiltro ?? '') === 'atendido'  ? 'selected' : '' }}>Atendidos</option>
                <option value="rechazado" {{ ($estadoFiltro ?? '') === 'rechazado' ? 'selected' : '' }}>Rechazados</option>
            </select>
        </form>
    </div>

    <div class="table-card">
        <div class="table-header">
            <h2>Reclamos</h2>
            <span class="total-badge">{{ $reclamos->total() }} registros</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Postulante</th>
                        <th>Detalle del Reclamo</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reclamos as $item)
                    <tr>
                        <td class="reg-cell">#{{ $item->id }}</td>
                        <td>
                            <div class="codigo-cell">{{ $item->codigo_postulante }}</div>
                            <div class="fecha-cell">{{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }}</div>
                        </td>
                        <td>
                            <div class="dirigido-cell">Dirigido a: {{ $item->dirigido ?? 'General' }}</div>
                            <div class="desc-cell">{{ $item->descripcion }}</div>
                        </td>
                        <td>
                            @if($item->estado === 'pendiente')
                                <span class="badge badge-pendiente">Pendiente</span>
                            @elseif($item->estado === 'atendido')
                                <span class="badge badge-atendido">Atendido</span>
                            @else
                                <span class="badge badge-rechazado">Rechazado</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn-evaluar"
                                onclick="abrirModal('{{ $item->id }}', '{{ $item->estado }}', '{{ $item->codigo_postulante }}')">
                                Evaluar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px;color:#888;">
                            No se registran reclamos en esta categoría.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-box">
            {{ $reclamos->appends(['filtroEstado' => $estadoFiltro])->links() }}
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalOverlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Resolver Reclamo <span id="modal-id" style="color:var(--azul-claro);"></span></h3>
            <p>Postulante: <strong id="modal-postulante"></strong></p>
        </div>
        <form id="formModal" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <label class="modal-label">Resolución</label>
                <select name="estado" id="modal-estado" class="modal-select">
                    <option value="pendiente">Pendiente</option>
                    <option value="atendido">Atendido (Procedente)</option>
                    <option value="rechazado">Rechazado (Desestimar)</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-aplicar">Aplicar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal(id, estado, codigo) {
    document.getElementById('modal-id').innerText = '#' + id;
    document.getElementById('modal-postulante').innerText = codigo;
    document.getElementById('modal-estado').value = estado;
    document.getElementById('formModal').action = `/admin/reclamos/${id}/actualizar`;
    document.getElementById('modalOverlay').classList.add('active');
}
function cerrarModal() {
    document.getElementById('modalOverlay').classList.remove('active');
}
document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>
@endsection