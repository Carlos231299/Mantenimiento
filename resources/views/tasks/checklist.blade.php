@extends('layouts.app')

@section('header', 'Checklist de Mantenimiento')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header Info -->
    <div class="bg-indigo-600 rounded-t-xl p-6 text-white shadow-lg">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-xl font-bold">Mantenimiento Preventivo</h3>
                <p class="text-indigo-100 mt-1">{{ $task->equipment->room->name ?? 'Sala S/N' }} - {{ $task->equipment->inventory_code }}</p>
            </div>
            <div class="bg-indigo-500/50 px-3 py-1 rounded-lg backdrop-blur-sm">
                <span class="text-sm font-medium">Ticket #{{ $task->id }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="bg-white rounded-b-xl shadow-sm border border-gray-100 border-t-0 overflow-hidden">
        @csrf
        @method('PUT')
        
        <!-- Hallazgos Preliminares (Si existen) -->
        @if(isset($task->checklist_data['preliminary_findings']) && count($task->checklist_data['preliminary_findings']) > 0)
            <div class="p-6 bg-amber-50 border-b border-amber-100">
                <h4 class="text-lg font-semibold text-amber-800 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Hallazgos Preliminares Reportados
                </h4>
                <ul class="list-disc list-inside text-sm text-amber-700 bg-white/50 p-3 rounded-lg border border-amber-200">
                    @foreach($task->checklist_data['preliminary_findings'] as $finding)
                        <li>{{ $finding }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Section: Hardware Check -->
        <div class="p-6 border-b border-gray-100">
            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                Inspección Física
            </h4>
            
            <div class="space-y-3 mb-6">
                <!-- Header -->
                <div class="flex items-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200 pb-2">
                    <div class="flex-1">Punto de Control</div>
                    <div class="w-20 text-center">Realizado</div>
                    <div class="w-20 text-center">No Aplica</div>
                </div>

                @foreach([
                    'cleaning' => 'Limpieza interna (Polvo)',
                    'peripherals' => 'Periféricos (Teclado/Mouse)',
                    'cables' => 'Cables y Conectores',
                    'screen' => 'Aplicar Silicona Protectora',
                    'cooler' => 'Ventiladores',
                    'thermal_paste' => 'Cambio Pasta Térmica'
                ] as $key => $label)
                <div class="flex items-center py-2 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                    <span class="flex-1 text-gray-700 font-medium text-sm">{{ $label }}</span>
                    <div class="w-20 text-center">
                        <input type="checkbox" name="checklist_data[hardware][{{ $key }}][checked]" class="h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300 checkbox-mutex" data-group="hw-{{ $key }}" onchange="toggleMutex(this)" {{ isset($task->checklist_data['hardware'][$key]['checked']) ? 'checked' : '' }}>
                    </div>
                    <div class="w-20 text-center">
                        <input type="checkbox" name="checklist_data[hardware][{{ $key }}][na]" class="h-5 w-5 text-gray-400 rounded focus:ring-gray-500 border-gray-300 checkbox-mutex" data-group="hw-{{ $key }}" onchange="toggleMutex(this)" {{ isset($task->checklist_data['hardware'][$key]['na']) ? 'checked' : '' }}>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Dynamic Hardware Items -->
            <div class="mb-6">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Otros Puntos de Hardware</label>
                <div id="hwCustomList" class="space-y-0">
                    @if(isset($task->checklist_data['hardware']['custom']))
                        @foreach($task->checklist_data['hardware']['custom'] as $uuid => $item)
                             @php 
                                $name = is_array($item) ? ($item['name'] ?? '') : $item;
                                $checked = is_array($item) ? isset($item['checked']) : true; 
                                $na = is_array($item) ? isset($item['na']) : false;
                                $uuid = is_array($item) ? $uuid : ('legacy_'.uniqid());
                             @endphp
                             <div class="flex items-center py-2 border-b border-gray-50 hover:bg-indigo-50 transition-colors" id="hw-custom-{{ $uuid }}">
                                <input type="hidden" name="checklist_data[hardware][custom][{{ $uuid }}][name]" value="{{ $name }}">
                                <span class="flex-1 text-indigo-700 font-medium text-sm pl-2 border-l-2 border-indigo-300">{{ $name }}</span>
                                <div class="w-20 text-center">
                                    <input type="checkbox" name="checklist_data[hardware][custom][{{ $uuid }}][checked]" class="h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300 checkbox-mutex" data-group="hw-custom-{{ $uuid }}" onchange="toggleMutex(this)" {{ $checked ? 'checked' : '' }}>
                                </div>
                                <div class="w-20 text-center">
                                    <input type="checkbox" name="checklist_data[hardware][custom][{{ $uuid }}][na]" class="h-5 w-5 text-gray-400 rounded focus:ring-gray-500 border-gray-300 checkbox-mutex" data-group="hw-custom-{{ $uuid }}" onchange="toggleMutex(this)" {{ $na ? 'checked' : '' }}>
                                </div>
                                <button type="button" onclick="removeCustomRow('hw-custom-{{ $uuid }}')" class="ml-2 text-red-400 hover:text-red-600 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="flex gap-2 mt-3">
                    <input type="text" id="hwInput" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs h-8 border px-2" placeholder="Agregar punto de control...">
                    <button type="button" onclick="addCustomRow('hw')" class="px-3 bg-gray-100 border border-gray-300 rounded hover:bg-gray-200 text-gray-600 text-xs font-medium whitespace-nowrap">
                        + Agregar
                    </button>
                </div>
            </div>

            <!-- Hardware Findings (Split) -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-2">Hallazgos de Hardware</label>
                <div class="flex gap-2 mb-2">
                    <input type="text" id="hwFindingInput" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-9 border px-3" placeholder="Ej. Cable SATA reemplazado...">
                    <button type="button" onclick="addFinding('hw')" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 font-medium text-xs">Agregar</button>
                </div>
                <div id="hwFindingsList" class="space-y-2">
                    @php
                        $hwFindings = $task->checklist_data['hardware']['findings'] ?? [];
                        // MIGRATION: If no HW findings but Legacy Findings exist, use them here.
                        $legacyFindings = $task->checklist_data['maintenance_findings'] ?? [];
                        if (empty($hwFindings) && !empty($legacyFindings)) {
                            $hwFindings = $legacyFindings;
                        }
                        // Fallback to text observations if absolutely nothing else
                        if (empty($hwFindings) && empty($legacyFindings) && $task->observations) {
                            $lines = explode("\n", $task->observations);
                            foreach($lines as $line) {
                                if(trim($line) && !str_contains($line, 'Reporte automático')) $hwFindings[] = trim($line, "- \t\n\r\0\x0B");
                            }
                        }
                    @endphp
                    @foreach($hwFindings as $index => $finding)
                        <div class="flex justify-between items-center bg-white p-2 rounded border border-gray-200" id="hw-finding-{{ $index }}">
                            <input type="hidden" name="checklist_data[hardware][findings][]" value="{{ $finding }}">
                            <span class="text-xs text-gray-700">• {{ $finding }}</span>
                            <button type="button" onclick="document.getElementById('hw-finding-{{ $index }}').remove()" class="text-red-500 hover:text-red-700 text-xs font-bold">X</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Section: Software Check -->
        <div class="p-6 border-b border-gray-100">
            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                Inspección de Software
            </h4>
             <div class="space-y-3 mb-6">
                <!-- Header -->
                <div class="flex items-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200 pb-2">
                    <div class="flex-1">Punto de Control</div>
                    <div class="w-20 text-center">Realizado</div>
                    <div class="w-20 text-center">No Aplica</div>
                </div>

                @foreach([
                    'antivirus' => 'Antivirus Actualizado',
                    'tmp_files' => 'Archivos Temporales',
                    'disk_opt' => 'Optimización Disco',
                    'drivers' => 'Drivers/Controladores',
                    'unauthorized_sw' => 'Remover Soft. No Autorizado',
                    'windows_update' => 'Windows Update'
                ] as $key => $label)
                <div class="flex items-center py-2 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                    <span class="flex-1 text-gray-700 font-medium text-sm">{{ $label }}</span>
                    <div class="w-20 text-center">
                        <input type="checkbox" name="checklist_data[software][{{ $key }}][checked]" class="h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300 checkbox-mutex" data-group="sw-{{ $key }}" onchange="toggleMutex(this)" {{ isset($task->checklist_data['software'][$key]['checked']) ? 'checked' : '' }}>
                    </div>
                    <div class="w-20 text-center">
                        <input type="checkbox" name="checklist_data[software][{{ $key }}][na]" class="h-5 w-5 text-gray-400 rounded focus:ring-gray-500 border-gray-300 checkbox-mutex" data-group="sw-{{ $key }}" onchange="toggleMutex(this)" {{ isset($task->checklist_data['software'][$key]['na']) ? 'checked' : '' }}>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Dynamic Software Items -->
            <div class="mb-6">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Otros Puntos de Software</label>
                <div id="swCustomList" class="space-y-0">
                    @if(isset($task->checklist_data['software']['custom']))
                        @foreach($task->checklist_data['software']['custom'] as $uuid => $item)
                             @php 
                                $name = is_array($item) ? ($item['name'] ?? '') : $item;
                                $checked = is_array($item) ? isset($item['checked']) : true; 
                                $na = is_array($item) ? isset($item['na']) : false;
                                $uuid = is_array($item) ? $uuid : ('legacy_'.uniqid());
                             @endphp
                             <div class="flex items-center py-2 border-b border-gray-50 hover:bg-indigo-50 transition-colors" id="sw-custom-{{ $uuid }}">
                                <input type="hidden" name="checklist_data[software][custom][{{ $uuid }}][name]" value="{{ $name }}">
                                <span class="flex-1 text-indigo-700 font-medium text-sm pl-2 border-l-2 border-indigo-300">{{ $name }}</span>
                                <div class="w-20 text-center">
                                    <input type="checkbox" name="checklist_data[software][custom][{{ $uuid }}][checked]" class="h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300 checkbox-mutex" data-group="sw-custom-{{ $uuid }}" onchange="toggleMutex(this)" {{ $checked ? 'checked' : '' }}>
                                </div>
                                <div class="w-20 text-center">
                                    <input type="checkbox" name="checklist_data[software][custom][{{ $uuid }}][na]" class="h-5 w-5 text-gray-400 rounded focus:ring-gray-500 border-gray-300 checkbox-mutex" data-group="sw-custom-{{ $uuid }}" onchange="toggleMutex(this)" {{ $na ? 'checked' : '' }}>
                                </div>
                                <button type="button" onclick="removeCustomRow('sw-custom-{{ $uuid }}')" class="ml-2 text-red-400 hover:text-red-600 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="flex gap-2 mt-3">
                    <input type="text" id="swInput" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs h-8 border px-2" placeholder="Agregar punto de control...">
                    <button type="button" onclick="addCustomRow('sw')" class="px-3 bg-gray-100 border border-gray-300 rounded hover:bg-gray-200 text-gray-600 text-xs font-medium whitespace-nowrap">
                        + Agregar
                    </button>
                </div>
            </div>

            <!-- Software Findings (Split) -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-2">Hallazgos de Software</label>
                <div class="flex gap-2 mb-2">
                    <input type="text" id="swFindingInput" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-9 border px-3" placeholder="Ej. Se desinstaló programa X...">
                    <button type="button" onclick="addFinding('sw')" class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 font-medium text-xs">Agregar</button>
                </div>
                <div id="swFindingsList" class="space-y-2">
                     @php
                        $swFindings = $task->checklist_data['software']['findings'] ?? [];
                    @endphp
                    @foreach($swFindings as $index => $finding)
                        <div class="flex justify-between items-center bg-white p-2 rounded border border-gray-200" id="sw-finding-{{ $index }}">
                            <input type="hidden" name="checklist_data[software][findings][]" value="{{ $finding }}">
                            <span class="text-xs text-gray-700">• {{ $finding }}</span>
                            <button type="button" onclick="document.getElementById('sw-finding-{{ $index }}').remove()" class="text-red-500 hover:text-red-700 text-xs font-bold">X</button>
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- Section: Final Verification -->
        <div class="p-6 bg-indigo-50 border-b border-indigo-100">
            <h4 class="text-lg font-bold text-indigo-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Verificación Final de Operatividad
            </h4>
            
            <p class="text-sm text-indigo-700 mb-4 font-medium">Después de las pruebas realizadas, ¿cuál es el estado final del equipo?</p>
            
            <div class="grid grid-cols-2 gap-4">
                <label class="relative flex flex-col p-4 bg-white rounded-xl border-2 cursor-pointer transition-all hover:shadow-md has-[:checked]:border-green-500 has-[:checked]:bg-green-50 group">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-black text-gray-700 group-has-[:checked]:text-green-700">OPERATIVO</span>
                        <input type="radio" name="final_status" value="operational" class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300" required {{ $task->equipment->status == 'operational' ? 'checked' : '' }}>
                    </div>
                    <p class="text-[10px] text-gray-400 group-has-[:checked]:text-green-600">El equipo enciende y funciona correctamente.</p>
                </label>

                <label class="relative flex flex-col p-4 bg-white rounded-xl border-2 cursor-pointer transition-all hover:shadow-md has-[:checked]:border-red-500 has-[:checked]:bg-red-50 group">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-black text-gray-700 group-has-[:checked]:text-red-700">CON FALLA</span>
                        <input type="radio" name="final_status" value="faulty" class="h-5 w-5 text-red-600 focus:ring-red-500 border-gray-300" required {{ $task->equipment->status == 'faulty' ? 'checked' : '' }}>
                    </div>
                    <p class="text-[10px] text-gray-400 group-has-[:checked]:text-red-600">Persisten problemas o no enciende.</p>
                </label>
            </div>

            <div class="mt-6 flex items-center pt-4 border-t border-indigo-200">
                <input type="checkbox" id="urgent" name="is_urgent" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded" {{ $task->is_urgent ? 'checked' : '' }}>
                <label for="urgent" class="ml-2 block text-xs text-red-700 font-bold uppercase tracking-tight">
                    Requiere reparación inmediata / Repuestos
                </label>
            </div>
        </div>

        <script>
            // Utility: Mutually Exclusive Checkboxes
            function toggleMutex(checkbox) {
                if (!checkbox.checked) return; // If unchecking, do nothing (allow both empty)

                const group = checkbox.dataset.group;
                // Find all checkboxes in this group
                const siblings = document.querySelectorAll(`.checkbox-mutex[data-group="${group}"]`);
                
                siblings.forEach(sib => {
                    if (sib !== checkbox) {
                        sib.checked = false;
                    }
                });
            }

            function addCustomRow(type) {
                const inputId = type === 'hw' ? 'hwInput' : 'swInput';
                const listId = type === 'hw' ? 'hwCustomList' : 'swCustomList';
                const section = type === 'hw' ? 'hardware' : 'software';
                
                const input = document.getElementById(inputId);
                const text = input.value.trim();
                
                if (text) {
                    const list = document.getElementById(listId);
                    const uuid = Date.now();
                    
                    const item = document.createElement('div');
                    item.className = "flex items-center py-2 border-b border-gray-50 hover:bg-indigo-50 transition-colors";
                    item.id = `${type}-custom-${uuid}`;
                    
                    // Note: Default new items to Not Checked, Not NA. User must click.
                    item.innerHTML = `
                        <input type="hidden" name="checklist_data[${section}][custom][${uuid}][name]" value="${text}">
                        <span class="flex-1 text-indigo-700 font-medium text-sm pl-2 border-l-2 border-indigo-300">${text}</span>
                        <div class="w-20 text-center">
                            <input type="checkbox" name="checklist_data[${section}][custom][${uuid}][checked]" class="h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300 checkbox-mutex" data-group="${type}-custom-${uuid}" onchange="toggleMutex(this)">
                        </div>
                        <div class="w-20 text-center">
                            <input type="checkbox" name="checklist_data[${section}][custom][${uuid}][na]" class="h-5 w-5 text-gray-400 rounded focus:ring-gray-500 border-gray-300 checkbox-mutex" data-group="${type}-custom-${uuid}" onchange="toggleMutex(this)">
                        </div>
                        <button type="button" onclick="removeCustomRow('${type}-custom-${uuid}')" class="ml-2 text-red-400 hover:text-red-600 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    `;
                    
                    list.appendChild(item);
                    input.value = '';
                    input.focus();
                }
            }

            function removeCustomRow(id) {
                document.getElementById(id).remove();
            }

            // New: Split Findings
            function addFinding(type) {
                const inputId = type === 'hw' ? 'hwFindingInput' : 'swFindingInput';
                const listId = type === 'hw' ? 'hwFindingsList' : 'swFindingsList';
                const section = type === 'hw' ? 'hardware' : 'software';

                const input = document.getElementById(inputId);
                const text = input.value.trim();

                if (text) {
                    const list = document.getElementById(listId);
                    const id = Date.now();

                    const item = document.createElement('div');
                    item.className = "flex justify-between items-center bg-white p-2 rounded border border-gray-200 mt-2";
                    item.id = `${type}-finding-${id}`;
                    item.innerHTML = `
                        <input type="hidden" name="checklist_data[${section}][findings][]" value="${text}">
                        <span class="text-xs text-gray-700">• ${text}</span>
                        <button type="button" onclick="document.getElementById('${type}-finding-${id}').remove()" class="text-red-500 hover:text-red-700 text-xs font-bold">X</button>
                    `;
                    list.appendChild(item);
                    input.value = '';
                    input.focus();
                }
            }
        </script>

        <!-- Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cancelar
            </a>
            
            <button type="submit" name="save_progress" value="true" class="px-4 py-2 bg-amber-100 border border-transparent rounded-lg text-sm font-medium text-amber-800 hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                Guardar Avance
            </button>

            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md shadow-indigo-500/20">
                Finalizar Mantenimiento
            </button>
        </div>
    </form>
</div>
@endsection
{{-- Removed Scripts --}}
