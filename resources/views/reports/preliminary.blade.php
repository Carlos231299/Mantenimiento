<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propuesta Mantenimiento - {{ $room->name }}</title>
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Inter', sans-serif; background: white; color: black; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body class="p-12 max-w-4xl mx-auto">
    
    <!-- Action Bar -->
    <div class="no-print fixed top-4 right-4 flex gap-2">
        <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">
            Imprimir / Guardar PDF
        </button>
        <button onclick="window.close()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded shadow hover:bg-gray-300">
            Cerrar
        </button>
    </div>

    <!-- Header -->
    <div class="border-b-2 border-slate-800 pb-4 mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 uppercase tracking-wide">Propuesta de Mantenimiento</h1>
            <p class="text-slate-500 mt-1">Departamento de Soporte Técnico</p>
        </div>
        <div class="text-right">
            <p class="text-sm font-bold text-slate-900">Fecha: {{ $date->format('d/m/Y') }}</p>
            <p class="text-lg font-bold text-indigo-600 mt-1">{{ $room->name }}</p>
            <p class="text-xs text-slate-500">{{ $room->location ?? 'Ubicación no definida' }}</p>
        </div>
    </div>

    <!-- Summary -->
    <div class="mb-8 bg-slate-50 p-4 rounded-lg border border-slate-200">
        <h2 class="text-sm font-bold text-slate-700 uppercase mb-2">Resumen de Estado</h2>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <span class="block text-2xl font-bold text-slate-900">{{ $room->equipments->count() }}</span>
                <span class="text-xs text-slate-500 uppercase">Total Equipos</span>
            </div>
            <div>
                <span class="block text-2xl font-bold text-red-600">{{ $faultyEquipment->count() }}</span>
                <span class="text-xs text-slate-500 uppercase">Equipos con Falla</span>
            </div>
            <div>
                <span class="block text-2xl font-bold text-amber-600">{{ $pendingTasks->count() }}</span>
                <span class="text-xs text-slate-500 uppercase">Tareas Pendientes</span>
            </div>
        </div>
    </div>

    <!-- Faulty Equipment List -->
    <div class="mb-8">
        <h3 class="text-lg font-bold text-slate-800 mb-4 border-l-4 border-red-500 pl-3">1. Equipos que requieren intervención</h3>
        
        @if($faultyEquipment->count() > 0)
        <table class="w-full text-sm text-left text-gray-500 border border-gray-200">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-3 border-b">Código</th>
                    <th class="px-4 py-3 border-b">Estado Actual</th>
                    <th class="px-4 py-3 border-b">Problema Reportado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($faultyEquipment as $pc)
                <tr class="bg-white border-b">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $pc->inventory_code }}</td>
                    <td class="px-4 py-3">
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-400">
                            {{ ucfirst($pc->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 italic">
                        {{ $pc->tasks->first()->observations ?? 'Sin detalles específicos' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-sm text-gray-500 italic p-4 bg-green-50 rounded border border-green-100">No hay equipos reportados con fallas críticas en esta sala.</p>
        @endif
    </div>

    <!-- Pending Tasks List -->
    <div class="mb-8">
        <h3 class="text-lg font-bold text-slate-800 mb-4 border-l-4 border-amber-500 pl-3">2. Plan de Trabajo (Tareas Pendientes)</h3>
        
        @if($pendingTasks->count() > 0)
        <ul class="space-y-4">
            @foreach($pendingTasks as $task)
            <li class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm break-inside-avoid">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mb-2">
                            Mantenimiento {{ $task->priority == 'critical' ? 'Crítico' : 'General' }}
                        </span>
                        <p class="text-sm font-bold text-gray-900">Equipo: {{ $task->equipment->inventory_code }}</p>
                        <p class="text-sm text-gray-600 mt-1">Acción requerida: Revisión técnica y diagnóstico.</p>
                    </div>
                    <span class="text-xs text-gray-400">Creada: {{ $task->created_at->format('d/m/Y') }}</span>
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <p class="text-sm text-gray-500 italic p-4 bg-green-50 rounded border border-green-100">No hay tareas de mantenimiento pendientes programadas.</p>
        @endif
    </div>

    <!-- Signatures -->
    <div class="mt-16 pt-8 border-t border-gray-200">
        <div class="grid grid-cols-2 gap-16">
            <div class="text-center">
                <div class="border-b border-black mb-2 h-16"></div>
                <p class="font-bold text-sm uppercase">Solicitado Por</p>
                <p class="text-xs text-gray-500">Encargado de Sala / Docente</p>
            </div>
            <div class="text-center">
                <div class="border-b border-black mb-2 h-16"></div>
                <p class="font-bold text-sm uppercase">Aprobado Por</p>
                <p class="text-xs text-gray-500">Jefe de Soporte Técnico</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-12 text-center text-xs text-gray-400 border-t pt-4">
        <p>Generado automáticamente por MantSystem v1.0 - {{ now()->format('d/m/Y H:i A') }}</p>
    </div>

</body>
</html>
