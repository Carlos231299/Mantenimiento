@extends('layouts.app')

@section('header', 'Reporte de Mantenimiento')

@section('content')
<style>
    @media print {
        /* Hide Layout Elements by ID */
        #app-sidebar, #app-header, #print-actions, nav, .no-print-section { 
            display: none !important; 
        }
        
        /* Reset Containers */
        body, html, #app, main {
            background: white !important;
            width: 100% !important;
            height: auto !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
            display: block !important;
        }
        
        /* Specific Fixes for the App Layout */
        .flex, .h-screen, .overflow-hidden {
            display: block !important;
            height: auto !important;
            overflow: visible !important;
        }
        
        /* Report Container */
        .max-w-4xl {
            max-width: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
        
        /* Ensure Colors Print */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden my-4 print:shadow-none print:my-0 print:rounded-none">
    
    <!-- Actions (Hidden on Print) -->
    <div id="print-actions" class="bg-gray-50 border-b border-gray-200 px-8 py-4 flex justify-between items-center no-print-section">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tasks.index') }}" class="text-gray-600 hover:text-gray-900 font-medium text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver
            </a>
            
            @if($task->status == 'completed')
            <a href="{{ route('tasks.checklist', $task->id) }}?edit_confirmed=1" 
               onclick="return confirm('ATENCIÓN: Este mantenimiento ya ha sido finalizado.\n\n¿Está seguro que desea editarlo? Esto podría alterar los registros históricos.')"
               class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center opacity-60 hover:opacity-100 transition-opacity">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Editar (Corrección)
            </a>
            @endif
        </div>

        <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium text-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z"></path></svg>
            Imprimir Reporte
        </button>
    </div>

    <!-- Report Content -->
    <div class="p-8 print:p-8">
        <!-- Header -->
        <div>
                <img src="{{ asset('images/logo.svg') }}" alt="MantSystem" class="h-10" align="center">
            </div>
        <div class="flex justify-between items-center border-b-2 border-indigo-600 pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 uppercase tracking-wide" align="center">Reporte Técnico</h1>
                <p class="text-sm text-gray-500 mt-1">Mantenimiento Preventivo</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-indigo-600">Ticket #{{ $task->id }}</div>
                <div class="text-sm text-gray-400">{{ $task->completed_at ? $task->completed_at->format('d/m/Y H:i') : 'En curso' }}</div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-6 mb-8 bg-gray-50 p-4 rounded-lg border border-gray-100 print:bg-white print:border-none print:p-0">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Ubicación</h3>
                <p class="text-gray-800 font-semibold text-lg">{{ $task->equipment->room->name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Equipo</h3>
                <p class="text-gray-800 font-semibold text-lg">{{ $task->equipment->inventory_code }}</p>
                <p class="text-sm text-gray-500">{{ $task->equipment->name ?? 'PC Genérico' }} - {{ $task->equipment->ip_address }}</p>
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Técnico Responsable</h3>
                <p class="text-gray-800 font-medium">{{ $task->technician->name ?? 'No asignado' }}</p>
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Estado Final</h3>
                <span class="inline-flex px-2 py-1 text-xs font-bold rounded {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $task->status === 'completed' ? 'COMPLETADO' : 'EN PROCESO' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left Column: Findings -->
            <div class="space-y-6">
                
                <!-- Preliminary Findings -->
                @if(isset($task->checklist_data['preliminary_findings']) && count($task->checklist_data['preliminary_findings']) > 0)
                <div class="border border-amber-200 rounded-lg overflow-hidden">
                    <div class="bg-amber-50 px-4 py-2 border-b border-amber-100 font-semibold text-amber-800 text-sm">
                        Hallazgos Preliminares
                    </div>
                    <ul class="p-4 list-disc list-inside text-sm text-gray-700 bg-white">
                        @foreach($task->checklist_data['preliminary_findings'] as $finding)
                            <li class="mb-1">{{ $finding }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Hardware Findings -->
                @php
                     // Use migration logic just for display safety
                    $hwFindings = $task->checklist_data['hardware']['findings'] ?? [];
                    if (empty($hwFindings) && !empty($task->checklist_data['maintenance_findings'])) {
                        $hwFindings = $task->checklist_data['maintenance_findings'];
                    }
                @endphp
                <div class="border border-indigo-200 rounded-lg overflow-hidden">
                    <div class="bg-indigo-50 px-4 py-2 border-b border-indigo-100 font-semibold text-indigo-800 text-sm">
                        Hallazgos durante el Mantenimiento Hardware
                    </div>
                    <ul class="p-4 list-disc list-inside text-sm text-gray-700 bg-white">
                        @forelse($hwFindings as $finding)
                            <li class="mb-1">{{ $finding }}</li>
                        @empty
                            <li class="list-none text-gray-400 italic">Sin hallazgos registrados.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Software Findings -->
                <div class="border border-blue-200 rounded-lg overflow-hidden">
                    <div class="bg-blue-50 px-4 py-2 border-b border-blue-100 font-semibold text-blue-800 text-sm">
                        Hallazgos durante el Mantenimiento Software
                    </div>
                    <ul class="p-4 list-disc list-inside text-sm text-gray-700 bg-white">
                        @forelse($task->checklist_data['software']['findings'] ?? [] as $finding)
                            <li class="mb-1">{{ $finding }}</li>
                        @empty
                            <li class="list-none text-gray-400 italic">Sin hallazgos registrados.</li>
                        @endforelse
                    </ul>
                </div>

            </div>

             <!-- Right Column: Checklist Summary -->
             <div>
                <h4 class="font-bold text-gray-800 mb-3 border-b border-gray-200 pb-1">Checklist de Ejecución</h4>
                
                <!-- Hardware -->
                <div class="mb-4">
                    <h5 class="text-xs font-bold text-gray-500 uppercase mb-2">Hardware</h5>
                    <table class="w-full text-sm">
                        @foreach(['cleaning', 'peripherals', 'cables', 'screen', 'cooler', 'thermal_paste'] as $key)
                            @php 
                                $checked = isset($task->checklist_data['hardware'][$key]['checked']);
                                $na = isset($task->checklist_data['hardware'][$key]['na']);
                                $label = match($key) {
                                    'cleaning' => 'Limpieza interna',
                                    'peripherals' => 'Periféricos',
                                    'cables' => 'Cables y Conectores',
                                    'screen' => 'Estado Pantalla',
                                    'cooler' => 'Coolers',
                                    'thermal_paste' => 'Pasta Térmica',
                                    default => $key
                                };
                            @endphp
                            <tr class="border-b border-gray-100 last:border-0">
                                <td class="py-1 text-gray-600">{{ $label }}</td>
                                <td class="py-1 text-right font-mono">
                                    @if($na) <span class="text-gray-400">N/A</span>
                                    @elseif($checked) <span class="text-green-600 font-bold">OK</span>
                                    @else <span class="text-red-300">--</span> @endif
                                </td>
                            </tr>
                        @endforeach
                        <!-- Custom HW Items -->
                        @if(isset($task->checklist_data['hardware']['custom']))
                            @foreach($task->checklist_data['hardware']['custom'] as $item)
                                @php 
                                    $name = is_array($item) ? ($item['name'] ?? '') : $item;
                                    $checked = is_array($item) ? isset($item['checked']) : true; 
                                    $na = is_array($item) ? isset($item['na']) : false;
                                @endphp
                                <tr class="border-b border-gray-100 last:border-0 bg-indigo-50/30">
                                    <td class="py-1 text-indigo-700 pl-2 border-l-2 border-indigo-200">{{ $name }}</td>
                                    <td class="py-1 text-right font-mono">
                                        @if($na) <span class="text-gray-400">N/A</span>
                                        @elseif($checked) <span class="text-green-600 font-bold">OK</span>
                                        @else <span class="text-red-300">--</span> @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>

                <!-- Software -->
                 <div class="mb-4">
                    <h5 class="text-xs font-bold text-gray-500 uppercase mb-2">Software</h5>
                    <table class="w-full text-sm">
                        @foreach(['antivirus', 'tmp_files', 'disk_opt', 'drivers', 'unauthorized_sw', 'windows_update'] as $key)
                            @php 
                                $checked = isset($task->checklist_data['software'][$key]['checked']);
                                $na = isset($task->checklist_data['software'][$key]['na']);
                                $label = match($key) {
                                    'antivirus' => 'Antivirus',
                                    'tmp_files' => 'Archivos Temp.',
                                    'disk_opt' => 'Optimización Disco',
                                    'drivers' => 'Drivers',
                                    'unauthorized_sw' => 'Soft. No Autorizado',
                                    'windows_update' => 'Windows Update',
                                    default => $key
                                };
                            @endphp
                            <tr class="border-b border-gray-100 last:border-0">
                                <td class="py-1 text-gray-600">{{ $label }}</td>
                                <td class="py-1 text-right font-mono">
                                    @if($na) <span class="text-gray-400">N/A</span>
                                    @elseif($checked) <span class="text-green-600 font-bold">OK</span>
                                    @else <span class="text-red-300">--</span> @endif
                                </td>
                            </tr>
                        @endforeach
                         <!-- Custom SW Items -->
                        @if(isset($task->checklist_data['software']['custom']))
                            @foreach($task->checklist_data['software']['custom'] as $item)
                                @php 
                                    $name = is_array($item) ? ($item['name'] ?? '') : $item;
                                    $checked = is_array($item) ? isset($item['checked']) : true; 
                                    $na = is_array($item) ? isset($item['na']) : false;
                                @endphp
                                <tr class="border-b border-gray-100 last:border-0 bg-blue-50/30">
                                    <td class="py-1 text-indigo-700 pl-2 border-l-2 border-indigo-200">{{ $name }}</td>
                                    <td class="py-1 text-right font-mono">
                                        @if($na) <span class="text-gray-400">N/A</span>
                                        @elseif($checked) <span class="text-green-600 font-bold">OK</span>
                                        @else <span class="text-red-300">--</span> @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Footer / Signature -->
        <div class="mt-12 pt-8 border-t border-gray-200 grid grid-cols-2 gap-12">
            <div class="text-center">
                <div class="h-16 border-b border-gray-300 mb-2"></div>
                <p class="text-xs font-bold text-gray-400 uppercase">Firma del Responsable del mantenimiento</p>
            </div>
            <div class="text-center">
                <div class="h-16 border-b border-gray-300 mb-2"></div>
                <p class="text-xs font-bold text-gray-400 uppercase">Firma recibido</p>
            </div>
        </div>
    </div>
</div>
@endsection
