<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte General de Mantenimiento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; background-color: white !important; }
            .no-print { display: none; }
            .page-break { page-break-before: always; }
        }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white p-6 max-w-5xl mx-auto text-gray-900">
    
    <!-- HEADER -->
    <div class="flex justify-between items-start border-b-4 border-indigo-600 pb-6 mb-8">
        <div class="flex items-center gap-4">
            <div class="bg-indigo-600 p-3 rounded-xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">Informe de Gestión</h1>
                <p class="text-indigo-600 font-bold tracking-widest text-xs">SISTEMA DE MANTENIMIENTO LABORAL</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-1">Fecha de Generación</p>
            <p class="text-sm font-bold bg-gray-100 px-3 py-1 rounded-lg border border-gray-200 inline-block">{{ $date->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- MAIN KPIs -->
    <div class="grid grid-cols-4 gap-4 mb-10">
        <div class="p-4 rounded-xl border-2 border-gray-100 bg-gray-50/50">
            <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Equipos Totales</p>
            <h3 class="text-2xl font-black">{{ $totalEquipment }}</h3>
        </div>
        <div class="p-4 rounded-xl border-2 border-red-100 bg-red-50/30">
            <p class="text-[10px] font-black text-red-400 uppercase mb-1">Con Falla / Mant.</p>
            <h3 class="text-2xl font-black text-red-600">{{ $faultyEquipment }}</h3>
        </div>
        <div class="p-4 rounded-xl border-2 border-amber-100 bg-amber-50/30">
            <p class="text-[10px] font-black text-amber-400 uppercase mb-1">Tareas Pendientes</p>
            <h3 class="text-2xl font-black text-amber-600">{{ $pendingTasksCount }}</h3>
        </div>
        <div class="p-4 rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
            <p class="text-[10px] font-black text-indigo-100 uppercase mb-1">Índice de Salud</p>
            <h3 class="text-2xl font-black">{{ $healthIndex }}%</h3>
        </div>
    </div>

    <!-- LOWER GRIDS -->
    <div class="grid grid-cols-2 gap-8 mb-10">
        <!-- Room Health Ranking -->
        <div>
            <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                <span class="w-2 h-4 bg-indigo-600 rounded-sm"></span>
                Salud por Sala (Ranking)
            </h2>
            <div class="space-y-4">
                @foreach($roomHealthRanking as $room)
                <div class="border border-gray-100 rounded-xl p-3 bg-white hover:shadow-sm transition-all">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-bold text-gray-800">{{ $room['name'] }}</span>
                        <span class="text-xs font-black {{ $room['health'] < 70 ? 'text-red-600' : 'text-green-600' }}">{{ $room['health'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden flex">
                         @php 
                            $color = 'bg-green-500';
                            if($room['health'] < 50) $color = 'bg-red-500';
                            elseif($room['health'] < 80) $color = 'bg-amber-500';
                        @endphp
                        <div class="{{ $color }} h-full" style="width: {{ $room['health'] }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1.5 text-[10px] text-gray-400 font-bold uppercase tracking-tighter">
                        <span>Op: {{ $room['total'] - $room['faulty'] }}</span>
                        <span>Fallas: {{ $room['faulty'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Priority Summary -->
        <div>
            <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                <span class="w-2 h-4 bg-indigo-600 rounded-sm"></span>
                Pendientes por Prioridad
            </h2>
            <div class="bg-gray-50 rounded-2xl p-6 border-2 border-dashed border-gray-200 h-full flex flex-col justify-center">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-red-600"></span>
                            <span class="text-sm font-bold text-gray-700">Prioridad Alta</span>
                        </div>
                        <span class="text-xl font-black text-red-600">{{ $priorityStats['high'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span class="text-sm font-bold text-gray-700">Prioridad Media</span>
                        </div>
                        <span class="text-xl font-black text-amber-600">{{ $priorityStats['normal'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <span class="text-sm font-bold text-gray-700">Prioridad Baja</span>
                        </div>
                        <span class="text-xl font-black text-blue-600">{{ $priorityStats['low'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <p class="text-xs text-gray-500 italic text-center leading-relaxed">
                        Existen <span class="font-bold text-gray-700">{{ $pendingTasksCount }}</span> tareas pendientes que requieren atención según su nivel de criticidad.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- RECENT MAINTENANCE TABLE -->
    <div class="page-break mt-12">
        <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4 flex items-center gap-2">
            <span class="w-2 h-4 bg-indigo-600 rounded-sm"></span>
            Bitácora de Mantenimientos (Último Mes)
        </h2>
        
        <table class="min-w-full border-collapse border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <thead>
                <tr class="bg-gray-900 text-white">
                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase">Fecha</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase">Equipo / Sala</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase">Técnico</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase">Estado Final</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black uppercase">Hallazgos</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($completedTasks as $task)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-xs font-bold text-gray-600">{{ $task->completed_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs font-black text-gray-900 truncate block">{{ $task->equipment->inventory_code }}</span>
                        <span class="text-[10px] text-gray-500 uppercase font-medium">{{ $task->equipment->room->name ?? '-' }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs font-medium text-gray-700">{{ $task->technician->name ?? 'Sistema' }}</td>
                    <td class="px-4 py-3">
                        @php $isOp = $task->equipment->status == 'operational'; @endphp
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase border {{ $isOp ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                            {{ $isOp ? 'OPERATIVO' : 'CON FALLA' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-[10px] text-gray-500 leading-tight">
                        @php
                            $findings = $task->checklist_data['hardware']['findings'] ?? [];
                            if(empty($findings)) $findings = $task->checklist_data['maintenance_findings'] ?? [];
                        @endphp
                        @if(!empty($findings))
                            <ul class="list-disc list-inside">
                                @foreach(array_slice($findings, 0, 2) as $f)
                                    <li>{{ Str::limit($f, 40) }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="italic">Mantenimiento preventivo estándar.</span>
                        @endif
                    </td>
                </tr>
                @empty
                 <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-gray-400 text-sm font-medium italic italic">No se han registrado cierres de mantenimiento en este período.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- SIGNATURES -->
    <div class="mt-20 pt-12 border-t-2 border-gray-100 flex justify-around">
        <div class="w-64 text-center">
            <div class="h-16 mb-2"></div> <!-- Space for signature -->
            <div class="border-b-2 border-gray-900 mb-2 mx-auto w-full"></div>
            <p class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Firma Supervisor</p>
            <p class="text-[9px] text-gray-400">Nombre y Cargo</p>
        </div>
        <div class="w-64 text-center">
            <div class="h-16 mb-2"></div> <!-- Space for signature -->
            <div class="border-b-2 border-gray-900 mb-2 mx-auto w-full"></div>
            <p class="text-[10px] font-black text-gray-900 uppercase tracking-widest">Responsable de Soporte</p>
            <p class="text-[9px] text-gray-400">Firma Autorizada</p>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="mt-16 text-center text-[9px] text-gray-300 font-bold uppercase tracking-widest">
        Documento generado automáticamente por el Sistema de Mantenimiento © {{ date('Y') }}
    </div>

    <!-- FLOATING PRINT BUTTON (Hidden on Print) -->
    <div class="fixed bottom-8 left-1/2 -translate-x-1/2 no-print">
        <button onclick="window.print()" class="bg-indigo-600 text-white px-8 py-3 rounded-2xl shadow-2xl hover:bg-indigo-700 font-black flex items-center gap-3 transition-all hover:scale-105 active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            IMPRIMIR REPORTE DE GESTIÓN
        </button>
    </div>

</body>
</html>
