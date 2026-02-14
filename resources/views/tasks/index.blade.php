@extends('layouts.app')

@section('header', 'Gestión de Tareas')

@section('content')
<div class="space-y-6">
    
    <!-- Dynamic Room Navigation Pills -->
    <div class="flex flex-wrap gap-2 pb-4 mb-2 overflow-x-auto no-scrollbar pt-2">
        <a href="{{ route('tasks.index') }}" 
           class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 shadow-sm border {{ !$currentRoomId ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-200' : 'bg-white text-gray-600 border-gray-100 hover:border-indigo-300 hover:text-indigo-600' }}">
            Todas las Salas
        </a>
        @foreach($rooms as $room)
            <a href="{{ route('tasks.index', ['room_id' => $room->id]) }}" 
               class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 shadow-sm border {{ $currentRoomId == $room->id ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-200' : 'bg-white text-gray-600 border-gray-100 hover:border-indigo-300 hover:text-indigo-600' }}">
                {{ $room->name }}
            </a>
        @endforeach
    </div>

    <!-- ROOM GRID -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        @forelse ($rooms as $room)
            @php 
                $roomTasks = $groupedTasks[$room->id] ?? collect();
                if ($currentRoomId && $room->id != $currentRoomId) continue;
                if ($roomTasks->isEmpty() && !$currentRoomId) continue; // Skip empty rooms in "All" view unless filtered
            @endphp
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group hover:shadow-md transition-shadow">
                <!-- Room Header -->
                <div class="p-5 bg-slate-900 text-white flex justify-between items-center border-b border-slate-800">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center mr-4 border border-indigo-500/30">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg tracking-tight">{{ $room->name }}</h3>
                            <p class="text-xs text-slate-400 font-medium">Laboratorio de Cómputo</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="block text-2xl font-black text-indigo-400 leading-none">{{ $roomTasks->count() }}</span>
                        <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">Tareas</span>
                    </div>
                </div>

                <!-- Progress Bar Layer -->
                @php
                    $roomDone = $roomTasks->where('status', 'completed')->count();
                    $roomTotal = $roomTasks->count();
                    $percent = $roomTotal > 0 ? ($roomDone / $roomTotal) * 100 : 0;
                @endphp
                <div class="h-1.5 bg-slate-800 w-full overflow-hidden">
                    <div class="h-full bg-indigo-500 transition-all duration-500" style="width: {{ $percent }}%"></div>
                </div>

                <!-- Task Cards -->
                <div class="p-4 bg-gray-50/50 flex-1 space-y-3 max-h-[500px] overflow-y-auto custom-scrollbar">
                    @forelse ($roomTasks as $task)
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:border-indigo-200 transition-all group/item relative overflow-hidden">
                            <!-- Priority Indicator Strip -->
                            @php
                                $color = match($task->priority) {
                                    'low' => 'bg-gray-400',
                                    'normal' => 'bg-blue-500',
                                    'high' => 'bg-amber-500',
                                    'critical' => 'bg-red-500',
                                };
                            @endphp
                            <div class="absolute left-0 top-0 bottom-0 w-1 {{ $color }}"></div>

                            <div class="flex justify-between items-start pl-1">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-black text-gray-800 uppercase tracking-tight">#{{ $task->id }} - {{ $task->equipment->inventory_code }}</span>
                                        @if($task->equipment->is_teacher_pc)
                                            <span class="px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase">Docente</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center text-[11px] text-gray-400 font-medium italic">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Asignada {{ $task->created_at->diffForHumans() }}
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2">
                                    @php
                                        $statusConfig = match($task->status) {
                                            'pending' => ['label' => 'Pendiente', 'css' => 'bg-gray-100 text-gray-600 border-gray-200'],
                                            'in_progress' => ['label' => 'En Mantenimiento', 'css' => 'bg-amber-50 text-amber-700 border-amber-200 shadow-sm shadow-amber-100'],
                                            'completed' => ['label' => 'Completada', 'css' => 'bg-green-50 text-green-700 border-green-200'],
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase border {{ $statusConfig['css'] }}">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex -space-x-2">
                                    @if($task->technician)
                                        <div class="w-7 h-7 rounded-full bg-slate-800 border-2 border-white flex items-center justify-center text-[10px] font-bold text-white shadow-sm" title="Técnico: {{ $task->technician->name }}">
                                            {{ strtoupper(substr($task->technician->name, 0, 2)) }}
                                        </div>
                                    @else
                                        <div class="w-7 h-7 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-gray-400 shadow-sm italic" title="Sin asignar">
                                            S/A
                                        </div>
                                    @endif
                                </div>

                                <a href="{{ $task->status == 'completed' ? route('tasks.show', $task->id) : route('tasks.checklist', $task->id) }}" 
                                   class="inline-flex items-center px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $task->status == 'completed' ? 'text-gray-600 bg-white border border-gray-200 hover:bg-gray-50' : 'text-white bg-indigo-600 shadow-md shadow-indigo-200 hover:bg-indigo-700 hover:translate-x-1' }}">
                                    <span>{{ $task->status == 'completed' ? 'Ver Reporte' : ($task->status == 'in_progress' ? 'Continuar' : 'Comenzar') }}</span>
                                    <svg class="w-3 h-3 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <p class="text-xs text-gray-400 font-medium">No hay mantenimiento activo aquí</p>
                        </div>
                    @endforelse
                </div>

                <!-- Card Footer Stats -->
                <div class="p-4 bg-white border-t border-gray-50 flex items-center bg-gray-50/20 px-6 py-3">
                    <div class="flex items-center text-[10px] font-bold text-gray-400 uppercase tracking-widest mr-auto">
                        <div class="w-2 h-2 rounded-full bg-green-500 mr-2"></div>
                        {{ $roomDone }} / {{ $roomTotal }} Listos
                    </div>
                    <div class="text-[10px] font-black text-indigo-600">
                        {{ round($percent) }}%
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">No se encontraron tareas</h3>
                <p class="text-gray-500 mt-2">Prueba cambiando el filtro o seleccionando otra sala.</p>
            </div>
        @endforelse
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
</div>
@endsection
