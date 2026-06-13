@extends('layouts.app')

@section('title', 'Sistema CUP FICCT - Atender Reclamos')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<div class="w-full px-6 py-6 text-gray-700 antialiased text-xs">

    <div
        class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 bg-white p-5 rounded-xl shadow-xs border border-gray-100">
        <div>
            <span class="text-[10px] font-black uppercase text-gray-400 tracking-wider block mb-1">Módulo de
                Auditoría</span>
            <h1 class="text-xl font-black text-[#0f4c81] tracking-wide m-0">Bandeja de Reclamos</h1>
        </div>

        <form method="GET" action="{{ url('/admin/reclamos') }}" class="flex items-center gap-2 m-0">
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Estado:</span>
            <select name="filtroEstado" onchange="this.form.submit()"
                class="bg-gray-50 border border-gray-200 text-gray-700 text-[11px] rounded-full px-4 py-1.5 font-semibold focus:outline-none cursor-pointer">
                <option value="Todos" {{ ($estadoFiltro ?? '' )=='Todos' ? 'selected' : '' }}>Todos</option>
                <option value="pendiente" {{ ($estadoFiltro ?? '' )=='pendiente' ? 'selected' : '' }}>Pendientes
                </option>
                <option value="atendido" {{ ($estadoFiltro ?? '' )=='atendido' ? 'selected' : '' }}>Atendidos</option>
                <option value="rechazado" {{ ($estadoFiltro ?? '' )=='rechazado' ? 'selected' : '' }}>Rechazados
                </option>
            </select>
        </form>
    </div>

    @if(session('success'))
    <div
        class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 mb-6 rounded-xl text-[11px] font-bold flex items-center gap-2 shadow-xs">
        <span
            class="bg-emerald-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px]">✓</span>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse m-0">
                <thead>
                    <tr
                        class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold uppercase text-gray-400 tracking-wider">
                        <th class="px-6 py-4">Reg</th>
                        <th class="px-6 py-4">Postulante</th>
                        <th class="px-6 py-4">Detalle del Reclamo</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-[12px] text-gray-600">
                    @forelse($reclamos as $item)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-400 font-mono">
                            #{{ $item->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-gray-800">{{ $item->codigo_postulante }}</div>
                            <div class="text-[10px] text-gray-400">
                                {{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-[11px] text-[#0f4c81] font-semibold mb-0.5">Dirigido a: {{ $item->dirigido
                                ?? 'General' }}</div>
                            <p class="text-gray-600 text-[11px] max-w-2xl break-words m-0 lh-relaxed">{{
                                $item->descripcion }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-[10px] font-bold uppercase px-3 py-1 rounded-full inline-block
                                @if($item->estado === 'pendiente') bg-amber-100 text-amber-800
                                @elseif($item->estado === 'atendido') bg-emerald-100 text-emerald-800
                                @else bg-rose-100 text-rose-800 @endif">
                                {{ $item->estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button
                                onclick="abrirModalResolucion('{{ $item->id }}', '{{ $item->estado }}', '{{ $item->codigo_postulante }}')"
                                class="px-4 py-1 bg-[#0f4c81] hover:bg-[#0b355c] text-white text-[11px] font-bold rounded-full transition cursor-pointer border-0">
                                Evaluar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            No se registran solicitudes pendientes en esta categoría.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reclamos->hasPages())
        <div
            class="p-6 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4 pb-8">
            <div class="text-gray-400 text-[11px] font-semibold">
                Mostrando {{ $reclamos->firstItem() }} al {{ $reclamos->lastItem() }} de {{ $reclamos->total() }}
                resultados
            </div>
            <div class="pr-2 text-gray-500 tracking-normal structure-pagination">
                {{ $reclamos->appends(['filtroEstado' => $estadoFiltro])->links() }}
            </div>
        </div>
        @endif
    </div>

    <div class="h-10"></div>
</div>

<div id="modalResolucion"
    class="fixed inset-0 bg-black/40 backdrop-blur-xs flex items-center justify-center hidden z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm overflow-hidden border border-gray-200">
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-bold text-gray-800 m-0">Resolver Reclamo <span id="display-id"
                    class="text-[#0f4c81]"></span></h2>
            <p class="text-[10px] text-gray-400 mt-0.5 mb-0">Postulante: <span id="display-postulante"
                    class="font-mono font-bold text-gray-600"></span></p>
        </div>

        <form id="formActualizarReclamo" method="POST" class="p-5 space-y-4 m-0">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Resolución
                    Judicial:</label>
                <select name="estado" id="select-estado"
                    class="w-full bg-gray-50 border border-gray-300 rounded-xl p-2.5 text-[11px] font-semibold focus:outline-none text-gray-700 cursor-pointer">
                    <option value="pendiente">Pendiente</option>
                    <option value="atendido">Atendido (Procedente)</option>
                    <option value="rechazado">Rechazado (Desestimar)</option>
                </select>
            </div>

            <div class="flex justify-between items-center pt-2">
                <button type="button" onclick="cerrarModalResolucion()"
                    class="text-[10px] font-bold text-gray-400 hover:text-gray-600 transition uppercase tracking-wider cursor-pointer bg-transparent border-0">
                    (cancelar)
                </button>
                <button type="submit"
                    class="text-[10px] font-bold text-[#0f4c81] hover:text-blue-900 transition uppercase tracking-wider cursor-pointer bg-transparent border-0">
                    (aplicar firma)
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalResolucion(id, estadoActual, codigoPostulante) {
        document.getElementById('display-id').innerText = '#' + id;
        document.getElementById('display-postulante').innerText = codigoPostulante;
        document.getElementById('select-estado').value = estadoActual;

        const form = document.getElementById('formActualizarReclamo');
        form.action = `/admin/reclamos/${id}/actualizar`;

        document.getElementById('modalResolucion').classList.remove('hidden');
    }

    function cerrarModalResolucion() {
        document.getElementById('modalResolucion').classList.add('hidden');
    }
</script>
<style>
    /* 1. Reducimos el margen y el padding para que no se separen de más */
    .structure-pagination nav div:last-child span a,
    .structure-pagination nav div:last-child span span,
    .structure-pagination nav ul li a,
    .structure-pagination nav ul li span {
        margin-left: 0px !important;
        /* Reducido para juntarlos más */
        margin-right: 0px !important;
        /* Reducido para juntarlos más */
        padding-left: 8px !important;
        /* Ancho interno ideal para cada número */
        padding-right: 8px !important;
        /* Ancho interno ideal para cada número */
        padding-top: 3px !important;
        padding-bottom: 3px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* 2. Ajustamos el contenedor de la barra oscura */
    .structure-pagination nav div:last-child {
        display: flex !important;
        gap: 2px !important;
        /* Espacio mínimo entre bloques */
        padding: 2px 4px !important;
    }
</style>
@endsection