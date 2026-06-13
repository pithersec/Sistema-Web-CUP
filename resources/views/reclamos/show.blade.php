@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-3xl px-4 py-6">
    <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
        <div class="p-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Detalle del Reclamo #{{ $reclamo->id }}</h2>
                <p class="text-xs text-gray-400">Registrado el {{ $reclamo->fecha->format('d/m/Y a las H:i') }}</p>
            </div>
            <a href="{{ route('reclamos.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">← Volver
                al listado</a>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="block text-xs font-bold uppercase text-gray-400 tracking-wider">Código
                        Postulante</span>
                    <p class="font-mono font-bold text-gray-800 mt-1">{{ $reclamo->codigo_postulante }}</p>
                    <p class="text-gray-600 text-xs">{{ $reclamo->postulante?->datosPersonales?->nombre ?? 'Nombre no
                        asociado' }}</p>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase text-gray-400 tracking-wider">Dirigido A</span>
                    <p class="text-gray-800 font-medium mt-1">{{ $reclamo->dirigido ?? 'Administración General CUP' }}
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <span class="block text-xs font-bold uppercase text-gray-400 tracking-wider mb-2">Descripción del
                    Motivo</span>
                <div
                    class="bg-gray-50 rounded-xl p-4 text-gray-700 leading-relaxed whitespace-pre-wrap text-sm border border-gray-100">
                    {{ $reclamo->descripcion }}
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4 grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                <div>
                    <span class="block text-xs font-bold uppercase text-gray-400 tracking-wider">Último Encargado</span>
                    <p class="text-gray-600 text-sm mt-1 font-mono">{{ $reclamo->registro_personal ?? 'Sin atención
                        previa' }}</p>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase text-gray-400 tracking-wider mb-2">Dictamen / Cambiar
                        Estado</span>
                    <form action="{{ route('reclamos.update', $reclamo->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <select name="estado" onchange="this.form.submit()" class="w-full text-sm font-semibold rounded-xl px-3 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500
                                @if($reclamo->estado === 'pendiente') bg-amber-50 text-amber-700 border-amber-200 
                                @elseif($reclamo->estado === 'atendido') bg-green-50 text-green-700 border-green-200 
                                @else bg-red-50 text-red-700 border-red-200 @endif">
                            <option value="pendiente" {{ $reclamo->estado === 'pendiente' ? 'selected' : '' }}>Pendiente
                            </option>
                            <option value="atendido" {{ $reclamo->estado === 'atendido' ? 'selected' : '' }}>Atendido
                            </option>
                            <option value="rechazado" {{ $reclamo->estado === 'rechazado' ? 'selected' : '' }}>Rechazado
                            </option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection