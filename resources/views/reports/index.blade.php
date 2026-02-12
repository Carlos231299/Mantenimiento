@extends('layouts.app')

@section('header', 'Informes y Métricas')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Bar -->
    <div class="flex justify-end mb-4">
        <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-2 bg-white p-2 rounded-lg shadow-sm border border-gray-100">
            <label for="room_id" class="text-sm font-medium text-gray-600">Filtrar por Sala:</label>
            <select name="room_id" id="room_id" onchange="this.form.submit()" class="border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todas las Salas</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" {{ isset($roomId) && $roomId == $room->id ? 'selected' : '' }}>
                        {{ $room->name }}
                    </option>
                @endforeach
            </select>
            
            @if(isset($roomId) && $roomId)
            <a href="{{ route('reports.preliminary', ['room_id' => $roomId]) }}" target="_blank" class="ml-2 px-3 py-2 bg-indigo-600 text-white text-xs font-bold rounded hover:bg-indigo-700 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Generar Propuesta
            </a>
            @endif
        </form>
    </div>
    
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Salas</p>
            <h4 class="text-3xl font-bold text-gray-800">{{ $totalRooms }}</h4>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Total Equipos</p>
            <h4 class="text-3xl font-bold text-gray-800">{{ $totalEquipment }}</h4>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Equipos con Fallas</p>
            <h4 class="text-3xl font-bold text-red-600">{{ $faultyEquipment }}</h4>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500">Tareas Pendientes</p>
            <h4 class="text-3xl font-bold text-amber-600">{{ $pendingTasks }}</h4>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Top Faulty Equipment -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Equipos con Más Incidencias</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sala</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Fallas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topFaultyEquipment as $equipment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $equipment->inventory_code }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $equipment->room->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $equipment->tasks_count }} reportes
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 text-sm">Sin datos suficientes</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Recent Completed Tasks -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
             <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Mantenimientos Recientes</h3>
                <a href="{{ route('reports.print') }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Imprimir Reporte
                </a>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($completedTasks->take(5) as $task)
                <li class="p-4 hover:bg-gray-50">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <span class="inline-block h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-green-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $task->equipment->inventory_code }} - {{ $task->equipment->room->name ?? 'S/N' }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">
                                Técnico: {{ $task->technician->name ?? 'Sistema' }}
                            </p>
                        </div>
                        <div class="inline-flex items-center text-xs text-gray-500">
                            {{ $task->completed_at ? $task->completed_at->format('d/m/Y') : '' }}
                        </div>
                    </div>
                </li>
                @empty
                <li class="p-4 text-center text-gray-500 text-sm">No hay mantenimientos recientes.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
