<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Gestión de Mantenimiento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; background-color: white !important; }
            .no-print { display: none; }
            .page-break { page-break-before: always; }
        }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        canvas { max-width: 100% !important; height: auto !important; }
    </style>
</head>
<body class="bg-white p-6 max-w-5xl mx-auto text-gray-900">
    
    <!-- HEADER -->
    <div class="flex justify-between items-start border-b-4 border-slate-900 pb-6 mb-8">
        <div class="flex items-center gap-4">
            <div class="bg-slate-900 p-3 rounded-xl shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase">Informe de Gestión</h1>
                <p class="text-slate-500 font-bold tracking-widest text-xs uppercase">Reporte Técnico de Infraestructura TI</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Generado por el sistema</p>
            <p class="text-xs font-bold bg-gray-100 px-3 py-1 rounded border border-gray-200 inline-block font-mono">{{ $date->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- MAIN KPIs -->
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="p-4 rounded border border-gray-200 bg-gray-50/50">
            <p class="text-[10px] font-black text-gray-400 uppercase mb-1 tracking-tighter">Inventario Total</p>
            <h3 class="text-2xl font-black text-slate-800">{{ $totalEquipment }} <span class="text-xs font-medium text-gray-400">PCs</span></h3>
        </div>
        <div class="p-4 rounded border border-red-100 bg-red-50/30">
            <p class="text-[10px] font-black text-red-400 uppercase mb-1 tracking-tighter">Fallas / Mant.</p>
            <h3 class="text-2xl font-black text-red-600">{{ $faultyEquipmentCount }}</h3>
        </div>
        <div class="p-4 rounded border border-amber-100 bg-amber-50/30">
            <p class="text-[10px] font-black text-amber-500 uppercase mb-1 tracking-tighter">Tareas Pendientes</p>
            <h3 class="text-2xl font-black text-amber-600">{{ $pendingTasksCount }}</h3>
        </div>
        <div class="p-4 rounded bg-slate-900 text-white shadow-md">
            <p class="text-[10px] font-black text-slate-300 uppercase mb-1 tracking-tighter">Índice Salud</p>
            <h3 class="text-2xl font-black">{{ $healthIndex }}%</h3>
        </div>
    </div>

    <!-- EXECUTIVE SUMMARY & GRAPHS -->
    <div class="grid grid-cols-3 gap-8 mb-10 items-start">
        <!-- Text Summary Column -->
        <div class="col-span-2 space-y-6">
            <section>
                <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="w-1 h-3 bg-slate-900 rounded-px"></span>
                    Resumen Ejecutivo
                </h2>
                <div class="p-6 bg-white rounded border border-gray-200 text-sm italic text-gray-700 leading-relaxed shadow-sm">
                    {{ $summary }}
                </div>
            </section>

            @if($recommendations)
            <section>
                <h2 class="text-xs font-black text-slate-700 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="w-1 h-3 bg-slate-700 rounded-px"></span>
                    Recomendaciones Técnicas
                </h2>
                <div class="p-6 bg-slate-50 rounded border border-slate-200 text-sm font-medium text-slate-800 leading-relaxed shadow-sm">
                    {!! nl2br(e($recommendations)) !!}
                </div>
            </section>
            @endif
        </div>

        <!-- Charts Column -->
        <div class="bg-gray-50 border border-gray-200 rounded p-5 space-y-8">
            <div class="text-center">
                <p class="text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest italic">Distribución Operativa</p>
                <div class="h-32 flex justify-center">
                    <canvas id="healthChart"></canvas>
                </div>
            </div>
            <div class="text-center pt-4 border-t border-gray-200">
                <p class="text-[10px] font-black text-gray-400 uppercase mb-4 tracking-widest italic">Salud por Sala (%)</p>
                <div class="h-48 flex justify-center">
                    <canvas id="roomsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- CRITICAL / FAULTY EQUIPMENT (HIGH PRIORITY) -->
    @if($faultyEquipment->count() > 0)
    <div class="mb-10 p-6 bg-red-50 rounded border-2 border-red-100 page-break shadow-sm">
        <h2 class="text-xs font-black text-red-700 uppercase tracking-widest mb-4 flex items-center gap-3">
            <span class="w-2.5 h-2.5 bg-red-600 rounded-full animate-pulse"></span>
            Mantenimientos Correctivos Pendientes (Alta Prioridad)
        </h2>
        <div class="grid grid-cols-2 gap-4">
            @foreach($faultyEquipment as $eq)
            <div class="bg-white p-3 rounded border border-red-200 flex justify-between items-center shadow-sm">
                <div>
                    <span class="text-xs font-black text-slate-900">{{ $eq->inventory_code }}</span>
                    <p class="text-[10px] text-gray-500 uppercase font-bold">{{ $eq->room->name ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-0.5 rounded-sm text-[8px] font-black uppercase bg-red-600 text-white shadow-sm shadow-red-100">CON FALLA</span>
                    <p class="text-[9px] text-red-400 mt-1 italic font-medium">Inspección pendiente</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- LOG TABLE -->
    <div class="page-break">
        <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
            <span class="w-1 h-3 bg-slate-900 rounded-px"></span>
            Bitácora de Intervenciones Técnicas (Último Periodo)
        </h2>
        
        <table class="min-w-full border border-gray-200 rounded overflow-hidden shadow-sm">
            <thead class="bg-slate-900">
                <tr>
                    <th class="px-4 py-3 text-left text-[9px] font-black text-white uppercase tracking-wider">Fecha</th>
                    <th class="px-4 py-3 text-left text-[9px] font-black text-white uppercase tracking-wider">Equipo / Ubicación</th>
                    <th class="px-4 py-3 text-left text-[9px] font-black text-white uppercase tracking-wider">Técnico</th>
                    <th class="px-4 py-3 text-left text-[9px] font-black text-white uppercase tracking-wider">Estado</th>
                    <th class="px-4 py-3 text-left text-[9px] font-black text-white uppercase tracking-wider">Resultados del Mantenimiento</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($completedTasks as $task)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-4 py-3 text-[11px] font-bold text-slate-600 whitespace-nowrap">{{ $task->completed_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="text-[11px] font-black text-slate-900 block">{{ $task->equipment->inventory_code }}</span>
                        <span class="text-[9px] text-slate-400 font-bold uppercase">{{ $task->equipment->room->name ?? '-' }}</span>
                    </td>
                    <td class="px-4 py-3 text-[11px] font-medium text-slate-700">{{ $task->technician->name ?? 'Sistema' }}</td>
                    <td class="px-4 py-3">
                        @php $isOp = $task->equipment->status == 'operational'; @endphp
                        <span class="px-2 py-0.5 rounded-sm text-[8px] font-black border uppercase {{ $isOp ? 'bg-green-50 text-green-700 border-green-200 shadow-sm shadow-green-50' : 'bg-red-50 text-red-700 border-red-200 shadow-sm shadow-red-50' }}">
                            {{ $isOp ? 'OPERATIVO' : 'CON FALLA' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-[10px] text-slate-600 leading-tight">
                        @php
                            $findings = $task->checklist_data['hardware']['findings'] ?? [];
                            if(empty($findings)) $findings = $task->checklist_data['maintenance_findings'] ?? [];
                            
                            // Count checklist items
                            $hwKeys = ['cleaning', 'peripherals', 'cables', 'screen', 'cooler', 'thermal_paste'];
                            $swKeys = ['antivirus', 'tmp_files', 'disk_opt', 'drivers', 'unauthorized_sw', 'windows_update'];
                            
                            $hwDone = 0; $hwTotal = count($hwKeys);
                            foreach($hwKeys as $k) if(isset($task->checklist_data['hardware'][$k]['checked']) || isset($task->checklist_data['hardware'][$k]['na'])) $hwDone++;
                            
                            $swDone = 0; $swTotal = count($swKeys);
                            foreach($swKeys as $k) if(isset($task->checklist_data['software'][$k]['checked']) || isset($task->checklist_data['software'][$k]['na'])) $swDone++;
                            
                            // Custom items
                            if(isset($task->checklist_data['hardware']['custom'])) {
                                $hwTotal += count($task->checklist_data['hardware']['custom']);
                                $hwDone += count($task->checklist_data['hardware']['custom']); // Assume custom added are done
                            }
                            if(isset($task->checklist_data['software']['custom'])) {
                                $swTotal += count($task->checklist_data['software']['custom']);
                                $swDone += count($task->checklist_data['software']['custom']);
                            }
                        @endphp
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-bold border border-slate-200">HW: {{ $hwDone }}/{{ $hwTotal }}</span>
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-bold border border-slate-200">SW: {{ $swDone }}/{{ $swTotal }}</span>
                        </div>
                        <p class="font-bold text-slate-800">Mantenimiento Preventivo Integral.</p>
                        @if(!empty($findings))
                            <span class="text-slate-500 italic block mt-1">
                                <strong class="not-italic text-slate-600 uppercase text-[8px] tracking-tighter">Falla corregida:</strong> {{ Str::limit(implode(', ', $findings), 120) }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                 <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400 italic font-medium">No se registran intervenciones en el periodo seleccionado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- SIGNATURES -->
    <div class="mt-20 pt-16 border-t border-gray-100 flex justify-around">
        <div class="w-72 text-center border-t border-slate-900 pt-3">
            <p class="text-[9px] font-black tracking-widest text-slate-900 uppercase">RESPONSABLE</p>
            <p class="text-[8px] text-slate-400 font-bold mt-1 uppercase tracking-tighter">Firma de conformidad</p>
        </div>
        <div class="w-72 text-center border-t border-slate-900 pt-3">
            <p class="text-[9px] font-black tracking-widest text-slate-900 uppercase">ENCARGADO DE LAS SALAS DE CÓMPUTO</p>
            <p class="text-[8px] text-slate-400 font-bold mt-1 tracking-tighter uppercase">Validación de infraestructura</p>
        </div>
    </div>

    <script>
        // Use high-resolution scaling for charts
        const healthData = {
            labels: ['Operativos', 'Con Falla'],
            datasets: [{
                data: [{{ $totalEquipment - $faultyEquipmentCount }}, {{ $faultyEquipmentCount }}],
                backgroundColor: ['#0f172a', '#e11d48'], // Slate-900 and Rose-600
                borderWidth: 0,
                spacing: 5
            }]
        };

        const roomsLabel = @json(array_column($roomHealthRanking, 'name'));
        const roomsValues = @json(array_column($roomHealthRanking, 'health'));

        // Chart Global Configuration
        Chart.defaults.devicePixelRatio = 2; // ENFORCE HIGH RESOLUTION
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.font.size = 10;
        Chart.defaults.font.weight = '700';
        Chart.defaults.animation = false;

        // Health Pie Chart
        new Chart(document.getElementById('healthChart'), {
            type: 'doughnut',
            data: healthData,
            options: {
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true, color: '#475569' } } },
                cutout: '65%'
            }
        });

        // Rooms Bar Chart
        new Chart(document.getElementById('roomsChart'), {
            type: 'bar',
            data: {
                labels: roomsLabel,
                datasets: [{
                    label: '% Salud',
                    data: roomsValues,
                    backgroundColor: roomsValues.map(v => v < 70 ? '#e11d48' : '#0f172a'),
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                scales: { 
                    x: { max: 100, display: false },
                    y: { grid: { display: false }, border: { display: false }, ticks: { color: '#475569' } }
                },
                plugins: { legend: { display: false } }
            }
        });

        window.onload = () => {
            // Documento listo para impresión técnica
        };
    </script>

    <!-- PRINT BUTTON -->
    <div class="fixed bottom-10 left-1/2 -translate-x-1/2 no-print">
        <button onclick="window.print()" class="bg-slate-900 text-white px-10 py-4 rounded shadow-2xl font-black text-sm tracking-widest hover:bg-slate-800 active:scale-95 transition-all flex items-center gap-4">
            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            IMPRIMIR REPORTE TÉCNICO PDF
        </button>
    </div>

</body>
</html>

</body>
</html>
