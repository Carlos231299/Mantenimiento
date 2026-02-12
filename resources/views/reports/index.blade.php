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
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Salas</p>
            <h4 class="text-3xl font-black text-gray-800">{{ $totalRooms }}</h4>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Equipos</p>
            <h4 class="text-3xl font-black text-gray-800">{{ $totalEquipment }}</h4>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-red-500">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Con Fallas</p>
            <h4 class="text-3xl font-black text-red-600">{{ $faultyEquipment }}</h4>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-amber-500">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Pendientes</p>
            <h4 class="text-3xl font-black text-amber-600">{{ $pendingTasks }}</h4>
        </div>
        <div class="bg-indigo-600 p-6 rounded-xl shadow-md border border-indigo-500">
            <p class="text-xs font-bold text-indigo-100 uppercase tracking-wider mb-1">Índice de Salud</p>
            <h4 class="text-3xl font-black text-white">{{ $healthIndex }}%</h4>
            <div class="w-full bg-indigo-800 rounded-full h-1.5 mt-2">
                <div class="bg-white h-1.5 rounded-full" style="width: {{ $healthIndex }}%"></div>
            </div>
        </div>
    </div>

    <!-- Prepare Print Report Form -->
    <div class="bg-white rounded-xl shadow-sm border-2 border-indigo-100 overflow-hidden">
        <div class="p-4 bg-indigo-50 border-b border-indigo-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-600 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-indigo-900 uppercase tracking-tight">Preparar Reporte de Gestión Premium</h3>
            </div>
            <span class="text-[10px] font-bold text-indigo-400 uppercase">Incluye Gráficas y Resumen Ejecutivo</span>
        </div>
        <div class="p-6">
            <form action="{{ route('reports.print') }}" method="GET" target="_blank" class="space-y-4">
                <div>
                    <label for="recommendations" class="block text-xs font-bold text-gray-500 uppercase mb-2">Recomendaciones y Observaciones Técnicas</label>
                    <textarea 
                        name="recommendations" 
                        id="recommendations" 
                        rows="3" 
                        class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm p-4 bg-gray-50 shadow-inner"
                        placeholder="Escribe aquí las recomendaciones que aparecerán en el informe impreso... (Ej. Se recomienda reemplazo de baterías en Sala A)"
                    ></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-black text-white text-xs uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg shadow-indigo-600/20 transition-all hover:scale-105 active:scale-95 gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Generar Informe de Impresión
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Room Health Ranking (Only visible when no room filter is active) -->
        @if(!$roomId)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-2">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Estado de Salud por Sala</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Sala</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Salud</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Operativos</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Con Falla/Mant.</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($roomHealthRanking as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">{{ $item['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        @php 
                                            $color = 'bg-green-500';
                                            if($item['health'] < 50) $color = 'bg-red-500';
                                            elseif($item['health'] < 80) $color = 'bg-amber-500';
                                        @endphp
                                        <div class="{{ $color }} h-2 rounded-full" style="width: {{ $item['health'] }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold {{ str_replace('bg', 'text', $color) }}">{{ $item['health'] }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">{{ $item['total'] - $item['faulty'] }} / {{ $item['total'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold">{{ $item['faulty'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('reports.index', ['room_id' => $rooms->firstWhere('name', $item['name'])->id]) }}" class="text-indigo-600 hover:text-indigo-900">Ver Detalles</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
        <!-- Priority Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Prioridad de Pendientes</h3>
            </div>
            <div class="p-6 grid grid-cols-3 gap-4">
                <div class="text-center p-4 bg-red-50 rounded-xl border border-red-100">
                    <p class="text-xs font-bold text-red-400 uppercase">Alta</p>
                    <h5 class="text-2xl font-black text-red-600">{{ $priorityStats['high'] ?? 0 }}</h5>
                </div>
                <div class="text-center p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <p class="text-xs font-bold text-amber-400 uppercase">Media</p>
                    <h5 class="text-2xl font-black text-amber-600">{{ $priorityStats['normal'] ?? 0 }}</h5>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <p class="text-xs font-bold text-blue-400 uppercase">Baja</p>
                    <h5 class="text-2xl font-black text-blue-600">{{ $priorityStats['low'] ?? 0 }}</h5>
                </div>
            </div>
        </div>

        <!-- Top Faulty Equipment -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Equipos con Más Incidencias</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Equipo</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Total Fallas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topFaultyEquipment as $equipment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            {{ $equipment->inventory_code }} <br>
                            <span class="text-[10px] text-gray-400 uppercase">{{ $equipment->room->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-black rounded-full bg-red-100 text-red-800">
                                {{ $equipment->tasks_count }} reportes
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-center text-gray-500 text-sm">Sin datos suficientes</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Recent Completed Tasks -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-2">
             <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Mantenimientos Recientes (Último Mes)</h3>
                <a href="{{ route('reports.print') }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900 font-bold flex items-center bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 transition-all hover:shadow-md">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Imprimir reporte
                </a>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($completedTasks->take(10) as $task)
                <li class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <span class="inline-block h-10 w-10 rounded-xl bg-green-100 flex items-center justify-center text-green-600 border border-green-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">
                                {{ $task->equipment->inventory_code }} <span class="text-gray-400 mx-1">|</span> {{ $task->equipment->room->name ?? 'S/N' }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">
                                Técnico: <span class="font-semibold">{{ $task->technician->name ?? 'Sistema' }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Completado</p>
                            <span class="text-xs font-bold text-gray-700 bg-gray-100 px-2 py-1 rounded">
                                {{ $task->completed_at ? $task->completed_at->format('d/m/Y') : '' }}
                            </span>
                        </div>
                    </div>
                </li>
                @empty
                <li class="p-8 text-center text-gray-500 text-sm italic">No hay mantenimientos recientes registrados en el último mes.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
