<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Mantenimiento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white p-8 max-w-4xl mx-auto">
    
    <div class="flex justify-between items-end border-b-2 border-gray-800 pb-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reporte de Mantenimiento</h1>
            <p class="text-gray-600">Departamento de Soporte Técnico</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500">Fecha de Emisión</p>
            <p class="font-medium">{{ $date->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4 uppercase tracking-wider text-xs border-b border-gray-200 pb-1">Mantenimientos Realizados (Último Mes)</h2>
        
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Equipo</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sala</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Técnico</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Observacion</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($completedTasks as $task)
                <tr>
                    <td class="px-4 py-2 text-sm text-gray-900">{{ $task->completed_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-2 text-sm font-medium">{{ $task->equipment->inventory_code }}</td>
                    <td class="px-4 py-2 text-sm text-gray-600">{{ $task->equipment->room->name ?? '-' }}</td>
                    <td class="px-4 py-2 text-sm text-gray-600">{{ $task->technician->name ?? 'Sistema' }}</td>
                    <td class="px-4 py-2 text-sm text-gray-500 italic">{{ Str::limit($task->observations, 50) }}</td>
                </tr>
                @empty
                 <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No se encontraron registros.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-12 pt-8 border-t border-gray-200 grid grid-cols-2 gap-8">
        <div class="text-center">
            <div class="border-b border-gray-400 w-2/3 mx-auto mb-2"></div>
            <p class="text-xs text-gray-500 uppercase">Firma del Supervisor</p>
        </div>
        <div class="text-center">
            <div class="border-b border-gray-400 w-2/3 mx-auto mb-2"></div>
            <p class="text-xs text-gray-500 uppercase">Firma del Técnico Responsable</p>
        </div>
    </div>

    <div class="fixed bottom-4 right-4 no-print">
        <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 font-medium">
            Imprimir Reporte
        </button>
    </div>

</body>
</html>
