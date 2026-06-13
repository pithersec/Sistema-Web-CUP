<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema CUP FICCT - Reclamos</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-[#f0f4f8] font-sans min-h-screen">

    <div class="max-w-7xl mx-auto px-6 py-8">

        <div class="flex justify-between items-center mb-8 bg-white p-4 rounded-xl shadow-xs">
            <a href="{{ url('/') }}"
                class="px-5 py-1.5 rounded-full border border-gray-300 text-xs font-bold text-gray-600 hover:bg-gray-100 transition shadow-xs">
                ← Volver
            </a>

            <button onclick="abrirModalReclamo()"
                class="px-6 py-1.5 bg-[#0f4c81] hover:bg-[#0b355c] text-white text-xs font-bold rounded-full transition shadow-xs cursor-pointer">
                + Crear
            </button>
        </div>

        <div class="mb-6">
            <h1 class="text-2xl font-black text-[#0f4c81] tracking-wide">Reclamos</h1>
        </div>

        <div id="alerta-exito"
            class="bg-white border border-emerald-200 p-6 mb-6 rounded-2xl flex flex-col items-center justify-center text-center shadow-md max-w-sm mx-auto hidden transition-all duration-300">
            <div
                class="w-12 h-12 bg-emerald-500 text-white rounded-full flex items-center justify-center text-xl font-bold mb-3 shadow-xs">
                ✓
            </div>
            <h3 class="font-bold text-gray-800 text-lg">¡Bien!</h3>
            <p class="text-sm text-gray-500 mt-1">Reclamo creado exitosamente</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-400 tracking-wider">
                            <th class="px-6 py-4">Reg</th>
                            <th class="px-6 py-4">Motivo</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-cuerpo-reclamos" class="divide-y divide-gray-100 text-sm text-gray-600">
                        @forelse($reclamos as $item)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-700">
                                #{{ $item->id }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-[#0f4c81] font-semibold mb-0.5">Dirigido a: {{ $item->dirigido
                                    ?? 'General' }}</div>
                                <p class="text-gray-600">{{ $item->descripcion }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-xs font-bold uppercase px-3 py-1 rounded-full 
                                        @if($item->estado === 'pendiente') bg-amber-100 text-amber-800
                                        @elseif($item->estado === 'atendido') bg-emerald-100 text-emerald-800
                                        @else bg-rose-100 text-rose-800 @endif">
                                    {{ $item->estado }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr id="fila-vacia">
                            <td colspan="3" class="px-6 py-12 text-center text-gray-400">
                                No existen registros de reclamos en el sistema.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modalReclamo"
        class="fixed inset-0 bg-black/40 backdrop-blur-xs flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden border border-gray-200">

            <div class="p-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800">Reclamo</h2>
            </div>

            <div id="modal-errores"
                class="mx-5 mt-4 p-3 bg-rose-50 border border-rose-200 rounded-lg text-xs text-rose-700 font-medium hidden">
            </div>

            <form id="formCrearReclamo" class="p-5 space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Código :</label>
                    <input type="text" name="codigo_postulante" id="input_codigo"
                        placeholder="............................................................................."
                        class="w-full border-b border-gray-300 focus:border-[#0f4c81] py-1 text-sm focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Dirigido :</label>
                    <input type="text" name="dirigido" id="input_dirigido"
                        placeholder="............................................................................."
                        class="w-full border-b border-gray-300 focus:border-[#0f4c81] py-1 text-sm focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Motivo :</label>
                    <textarea name="descripcion" id="input_motivo" rows="3"
                        placeholder="............................................................................."
                        class="w-full border-b border-gray-300 focus:border-[#0f4c81] py-1 text-sm focus:outline-none transition resize-none"></textarea>
                </div>

                <div class="flex justify-between items-center pt-2">
                    <button type="button" onclick="cerrarModalReclamo()"
                        class="text-xs font-bold text-gray-400 hover:text-gray-600 transition uppercase tracking-wider cursor-pointer">
                        (cancelar)
                    </button>
                    <button type="submit"
                        class="text-xs font-bold text-[#0f4c81] hover:text-blue-900 transition uppercase tracking-wider cursor-pointer">
                        (crear)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalReclamo() {
                document.getElementById('modal-errores').classList.add('hidden');
                document.getElementById('formCrearReclamo').reset();
                document.getElementById('modalReclamo').classList.remove('hidden');
            }
    
            function cerrarModalReclamo() {
                document.getElementById('modalReclamo').classList.add('hidden');
            }
    
            // ENVÍO MEDIANTE FETCH (AJAX) APUNTANDO A TU RUTA ORIGINAL
            document.getElementById('formCrearReclamo').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const divErrores = document.getElementById('modal-errores');
                divErrores.classList.add('hidden');
                divErrores.innerHTML = '';
    
                const formData = new FormData(this);
    
                // AQUÍ: Cambiado a /reclamo para que coincida exactamente con tu controlador
                fetch("/reclamo", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // 1. Cerramos la ventana emergente
                        cerrarModalReclamo();
                        
                        // 2. Encendemos el cartel de Éxito solicitado
                        const alerta = document.getElementById('alerta-exito');
                        alerta.classList.remove('hidden');
                        
                        // 3. Recargamos la página luego de 2.5 segundos para ver el nuevo reclamo en la lista
                        setTimeout(() => {
                            window.location.reload();
                        }, 2500);
                    } else {
                        // Muestra los errores si no completó datos o el código no existe
                        divErrores.innerHTML = data.errors.join('<br>');
                        divErrores.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    divErrores.innerHTML = 'Faltan rellenar campos obligatorios o el código no existe en el sistema.';
                    divErrores.classList.remove('hidden');
                });
            });
    </script>
</body>

</html>