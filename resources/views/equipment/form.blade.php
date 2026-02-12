@extends('layouts.app')

@section('header', isset($equipment) ? 'Editar Equipo' : 'Nuevo Equipo')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Datos del Equipo</h3>
            @php
                $backRoute = isset($equipment) ? route('rooms.show', $equipment->room_id) : (isset($roomId) ? route('rooms.show', $roomId) : route('rooms.index'));
            @endphp
            <a href="{{ $backRoute }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">Volver a Sala</a>
        </div>
        
        <form action="{{ isset($equipment) ? route('equipment.update', $equipment->id) : route('equipment.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @if(isset($equipment))
                @method('PUT')
            @else
                <input type="hidden" name="room_id" value="{{ $roomId ?? '' }}">
            @endif

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="col-span-2 sm:col-span-2">
                    <label for="inventory_code" class="block text-sm font-medium text-gray-700">Serial</label>
                    <input type="text" name="inventory_code" id="inventory_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3" placeholder="PC-01-001" value="{{ old('inventory_code', $equipment->inventory_code ?? '') }}">
                </div>

                <div class="col-span-2">
                    <label for="specifications" class="block text-sm font-medium text-gray-700">Especificaciones</label>
                    <textarea name="specifications" id="specifications" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-3" placeholder="Intel Core i5, 16GB RAM, 512GB SSD...">{{ old('specifications', $equipment->specifications ?? '') }}</textarea>
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700">Estado Operativo</label>
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3">
                        <option value="operational" {{ (old('status', $equipment->status ?? '') == 'operational') ? 'selected' : '' }}>Funciona Correctamente</option>
                        <option value="maintenance" {{ (old('status', $equipment->status ?? '') == 'maintenance') ? 'selected' : '' }}>Mantenimiento Preventivo</option>
                        <option value="faulty" {{ (old('status', $equipment->status ?? '') == 'faulty') ? 'selected' : '' }}>No Funciona</option>
                    </select>
                </div>
                
                 

                <div class="col-span-2" id="findings-section">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hallazgos Preliminares (Opcional)</label>
                    
                    <div class="flex gap-2 mb-2">
                        <input type="text" id="findingInput" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3" placeholder="Ej. Pantalla parpadea, Teclado sucio...">
                        <button type="button" onclick="addFinding()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium text-sm">
                            Agregar
                        </button>
                    </div>

                    <div id="findingsList" class="space-y-2 mb-2">
                        <!-- Items agregados por JS -->
                    </div>
                    
                    <!-- Hidden inputs para enviar al backend -->
                    <div id="findingsInputs"></div>
                </div>
            </div>

            <script>
                function addFinding() {
                    const input = document.getElementById('findingInput');
                    const text = input.value.trim();
                    
                    if (text) {
                        const list = document.getElementById('findingsList');
                        const inputsContainer = document.getElementById('findingsInputs');
                        const id = Date.now(); // ID único simple

                        // 1. Agregar visualmente
                        const item = document.createElement('div');
                        item.className = "flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-200";
                        item.id = `finding-${id}`;
                        item.innerHTML = `
                            <span class="text-sm text-gray-700">• ${text}</span>
                            <button type="button" onclick="removeFinding(${id})" class="text-red-500 hover:text-red-700 text-xs font-bold">Eliminar</button>
                        `;
                        list.appendChild(item);

                        // 2. Agregar input hidden
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'findings[]';
                        hiddenInput.value = text;
                        hiddenInput.id = `input-${id}`;
                        inputsContainer.appendChild(hiddenInput);

                        // 3. Limpiar
                        input.value = '';
                        input.focus();
                    }
                }

                function removeFinding(id) {
                    document.getElementById(`finding-${id}`).remove();
                    document.getElementById(`input-${id}`).remove();
                }
            </script>

            <div class="pt-4 flex items-center justify-end border-t border-gray-100 mt-6">
                @if(isset($equipment))
                <button type="button" class="mr-auto text-sm text-red-600 hover:text-red-900 font-medium flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Eliminar Equipo
                </button>
                @endif
                <a href="{{ $backRoute }}" class="mr-3 text-sm text-gray-600 hover:text-gray-900 font-medium">Cancelar</a>
                <button type="submit" class="bg-indigo-600 border border-transparent rounded-lg shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-indigo-500/20">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
