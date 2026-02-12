@extends('layouts.app')

@section('header', 'Nueva Sala de Cómputo')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Configuración de la Sala</h3>
            <a href="{{ route('rooms.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">Volver al listado</a>
        </div>
        
        <form action="{{ route('rooms.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la Sala</label>
                    <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3" placeholder="Ej. Laboratorio A">
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Ubicación / Edificio</label>
                    <input type="text" name="location" id="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3" placeholder="Ej. Edificio B, Piso 2">
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Capacidad y Distribución</h4>
                    <p class="text-sm text-gray-500 mb-4">Define cuántos equipos de estudiante tendrá la sala y cómo se distribuirán.</p>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                         <div>
                            <label for="admin_capacity" class="block text-sm font-medium text-gray-700">Equipos Administrador/Docente</label>
                            <input type="number" name="admin_capacity" id="admin_capacity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3" value="1" min="1" required>
                        </div>
                        <div>
                            <label for="capacity" class="block text-sm font-medium text-gray-700">Equipos Estudiantes (Grilla)</label>
                            <input type="number" name="capacity" id="capacity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3" placeholder="Ej. 24" min="1" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="rows_left" class="block text-sm font-medium text-gray-700">Filas Ala Izquierda (Máx 3 PCs/Fila)</label>
                            <input type="number" name="rows_left" id="rows_left" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3" placeholder="0" min="0">
                        </div>
                        <div>
                            <label for="rows_right" class="block text-sm font-medium text-gray-700">Filas Ala Derecha (Máx 3 PCs/Fila)</label>
                            <input type="number" name="rows_right" id="rows_right" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3" placeholder="0" min="0">
                        </div>
                    </div>
                    
                    <!-- Feedback visual de cálculo -->
                    <div id="calculation-feedback" class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200 hidden">
                        <p class="text-sm" id="feedback-text"></p>
                    </div>

                    <script>
                        const capacityInput = document.getElementById('capacity');
                        const leftInput = document.getElementById('rows_left');
                        const rightInput = document.getElementById('rows_right');
                        const feedbackBox = document.getElementById('calculation-feedback');
                        const feedbackText = document.getElementById('feedback-text');

                        function validateCapacity() {
                            const capacity = parseInt(capacityInput.value) || 0;
                            const left = parseInt(leftInput.value) || 0;
                            const right = parseInt(rightInput.value) || 0;
                            
                            const maxSlots = (left * 3) + (right * 3);
                            
                            if (capacity > 0 || maxSlots > 0) {
                                feedbackBox.classList.remove('hidden');
                                
                                if (capacity > maxSlots) {
                                    feedbackBox.className = "mt-4 p-3 bg-red-50 rounded-lg border border-red-100";
                                    feedbackText.innerHTML = `<span class="font-bold text-red-700">Advertencia:</span> Tienes ${capacity} estudiantes pero solo ${maxSlots} espacios disponibles en la distribución actual. <br>Aumenta las filas.`;
                                } else {
                                    feedbackBox.className = "mt-4 p-3 bg-indigo-50 rounded-lg border border-indigo-100";
                                    feedbackText.innerHTML = `<span class="font-bold text-indigo-700">Correcto:</span> Se generarán ${capacity} equipos de estudiantes distribuidos en ${maxSlots} espacios disponibles. <br>${maxSlots - capacity} espacios quedarán vacíos.`;
                                }
                            } else {
                                feedbackBox.classList.add('hidden');
                            }
                        }

                        capacityInput.addEventListener('input', validateCapacity);
                        leftInput.addEventListener('input', validateCapacity);
                        rightInput.addEventListener('input', validateCapacity);
                    </script>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end border-t border-gray-100 mt-6">
                <button type="button" class="mr-3 text-sm text-gray-600 hover:text-gray-900 font-medium">Cancelar</button>
                <button type="submit" class="bg-indigo-600 border border-transparent rounded-lg shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-indigo-500/20">
                    Crear Sala
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
