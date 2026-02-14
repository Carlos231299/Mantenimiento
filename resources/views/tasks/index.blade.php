@extends('layouts.app')

@section('header', 'Gestión de Tareas')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Bar -->
    <div class="flex justify-end mb-4">
        <form method="GET" action="{{ route('tasks.index') }}" class="flex items-center gap-2 bg-white p-2 rounded-lg shadow-sm border border-gray-100">
            <label for="room_id" class="text-sm font-medium text-gray-600">Filtrar por Sala:</label>
            <select name="room_id" id="room_id" onchange="this.form.submit()" class="border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todas las Salas</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" {{ isset($roomId) && $roomId == $room->id ? 'selected' : '' }}>
                        {{ $room->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pendientes</p>
                <h4 class="text-3xl font-bold text-gray-800">{{ $pending }}</h4>
            </div>
            <div class="p-3 bg-amber-50 rounded-lg text-amber-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">En Proceso</p>
                <h4 class="text-3xl font-bold text-gray-800">{{ $inProgress }}</h4>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
        </div>
         <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Completadas (Total)</p>
                <h4 class="text-3xl font-bold text-gray-800">{{ $completed }}</h4>
            </div>
            <div class="p-3 bg-green-50 rounded-lg text-green-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Tasks Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="#" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Todas las tareas
            </a>
        </nav>
    </div>

    <!-- TASKS LIST GROUPED BY ROOM -->
    @forelse ($groupedTasks as $roomName => $roomTasks)
    <div class="mt-8 mb-4 flex items-center">
        <div class="bg-slate-800 text-white px-4 py-1.5 rounded-lg text-sm font-bold shadow-sm flex items-center">
            <svg class="w-4 h-4 mr-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            {{ $roomName }}
            <span class="ml-2 bg-slate-700 text-slate-300 px-2 py-0.5 rounded text-xs">{{ $roomTasks->count() }}</span>
        </div>
        <div class="ml-4 h-px bg-gray-200 flex-grow"></div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket / Equipo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha / Hace</th>
                         <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Acciones</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($roomTasks as $task)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-bold text-gray-900">#{{ $task->id }} - {{ $task->equipment->inventory_code }}</div>
                                    <div class="text-xs text-gray-500">{{ $task->equipment->is_teacher_pc ? 'PC de Docente' : 'PC de Estudiante' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $priorityClass = match($task->priority) {
                                    'low' => 'bg-gray-100 text-gray-800',
                                    'normal' => 'bg-blue-100 text-blue-800',
                                    'high' => 'bg-orange-100 text-orange-800',
                                    'critical' => 'bg-red-100 text-red-800',
                                };
                                $priorityLabel = match($task->priority) {
                                    'low' => 'Baja',
                                    'normal' => 'Normal',
                                    'high' => 'Alta',
                                    'critical' => 'Crítica',
                                };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $priorityClass }}">
                                {{ $priorityLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             @php
                                $statusClass = match($task->status) {
                                    'pending' => 'bg-gray-100 text-gray-800',
                                    'in_progress' => 'bg-amber-100 text-amber-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                };
                                 $statusLabel = match($task->status) {
                                    'pending' => 'Pendiente',
                                    'in_progress' => 'En Mantenimiento',
                                    'completed' => 'Completada',
                                };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $task->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($task->status == 'completed')
                                <a href="{{ route('tasks.show', $task->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-300 shadow-sm text-xs font-medium rounded bg-white focus:outline-none transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Ver Reporte
                                </a>
                            @else
                                <a href="{{ route('tasks.checklist', $task->id) }}" class="inline-flex items-center px-3 py-1.5 border border-indigo-600 text-indigo-600 hover:bg-indigo-50 shadow-sm text-xs font-medium rounded bg-white focus:outline-none transition-colors">
                                    {{ $task->status == 'in_progress' ? 'Continuar Mantenimiento' : 'Iniciar Mantenimiento' }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center text-gray-500">
        <p class="text-sm">No hay tareas pendientes en ninguna sala.</p>
    </div>
    @endforelse
</div>
@endsection
