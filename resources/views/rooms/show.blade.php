@extends('layouts.app')

@section('header', 'Distribución de Sala: ' . $room->name)

@section('content')
<div class="space-y-6">
    <!-- Header / Stats -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800">{{ $room->name }}</h3>
            <p class="text-sm text-gray-500">{{ $room->location ?? 'Sin ubicación definida' }}</p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-sm text-gray-600">Operativo</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span class="text-sm text-gray-600">Mantenimiento</span>
            </div>
             <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-sm text-gray-600">Falla</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('equipment.create', ['room_id' => $room->id]) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium shadow-lg shadow-indigo-500/20">
                + Agregar PC
            </a>
            
            <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta sala y todos sus equipos?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                    Eliminar Sala
                </button>
            </form>
        </div>
    </div>

    <div class="mt-8 flex flex-col items-center gap-4">
        <div class="inline-block bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
            <span class="text-xs text-gray-500 uppercase tracking-widest font-semibold">Zona de Profesor / Pizarra</span>
        </div>
        
        <div class="flex flex-wrap justify-center gap-8">
            @forelse($room->equipments->where('is_teacher_pc', true) as $teacherPc)
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 bg-indigo-50 rounded-xl shadow-md border-2 border-indigo-500 flex items-center justify-center p-2 cursor-pointer hover:scale-105 transition-transform relative group">
                        <img src="{{ asset('img/pc_icon.png') }}" alt="PC Profesor" class="w-full h-full object-contain">
                        @php
                            $activeTask = $teacherPc->activeTask;
                            $lastTask = $teacherPc->lastCompletedTask;
                            $actionUrl = route('equipment.edit', $teacherPc->id);
                            $actionText = 'EDITAR';

                            if ($activeTask) {
                                $actionUrl = route('tasks.checklist', $activeTask->id);
                                $actionText = 'CONTINUAR';
                            } elseif ($lastTask) {
                                $actionUrl = route('tasks.show', $lastTask->id);
                                $actionText = 'REPORTE';
                            }
                        @endphp
                        <a href="{{ $actionUrl }}" class="absolute inset-0 flex items-center justify-center bg-black/40 text-white opacity-0 group-hover:opacity-100 transition-opacity rounded-xl font-bold text-xs">
                            {{ $actionText }}
                        </a>
                    </div>
                    
                    <!-- Edit Pencil for direct edits if needed -->
                    @if($activeTask || $lastTask)
                    <a href="{{ route('equipment.edit', $teacherPc->id) }}" class="absolute top-0 right-0 p-1 bg-white rounded-full text-gray-400 hover:text-indigo-600 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity z-10" title="Editar Equipo">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </a>
                    @endif

                        @if($activeTask)
                             <div class="absolute -top-3 -left-2 bg-amber-500 text-white rounded-full p-1 shadow-md border-2 border-white z-20" title="En Mantenimiento">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                        @elseif($teacherPc->status == 'faulty')
                            <div class="absolute -top-3 -left-2 bg-red-500 text-white rounded-full p-1 shadow-md border-2 border-white z-20" title="Falla Reportada">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        @elseif($lastTask)
                            <div class="absolute -top-3 -left-2 bg-green-500 text-white rounded-full p-1 shadow-md border-2 border-white z-20" title="Mantenimiento Completado">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        @else
                             <!-- New / Unregistered -->
                            <div class="absolute -top-3 -left-2 bg-blue-500 text-white rounded-full p-1 shadow-md border-2 border-white z-20" title="Sin Historial">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                        @endif
                        <span class="mt-2 text-xs font-bold text-white bg-indigo-600 px-3 py-1 rounded-full shadow-sm uppercase">
                            {{ $teacherPc->inventory_code }}
                        </span>
                    <span class="text-[10px] text-gray-500 mt-1 font-semibold uppercase">{{ $teacherPc->status }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400 italic">Sin PC Admin Asignado</p>
            @endforelse
        </div>
    </div>

    <!-- ROOM CONTAINER -->
    <div class="bg-slate-100 p-8 rounded-2xl shadow-inner border border-slate-200 overflow-x-auto">
        <div class="min-w-[800px] flex justify-center">
            <!-- LAYOUT GRID -->
            <div class="flex gap-8">
                
                <!-- ALA IZQUIERDA (Left Wing) -->
                <div class="flex flex-col gap-6">
                    <h4 class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Ala Izquierda</h4>

                    @for ($row = 1; $row <= $room->rows_left; $row++)
                        <div class="flex gap-4">
                            @for ($col = 6; $col >= 4; $col--)
                                @php 
                                    $pcNum = ($row - 1) * 6 + $col;
                                    $pc = $room->equipments->firstWhere('position_index', $pcNum);
                                    
                                    // Determinar clases según estado
                                    $borderClass = 'border-gray-300 bg-white'; // Default vacio
                                    if ($pc) {
                                        switch($pc->status) {
                                            case 'operational': $borderClass = 'border-green-400 bg-green-50'; break;
                                            case 'maintenance': $borderClass = 'border-amber-400 bg-amber-50'; break;
                                            case 'faulty': $borderClass = 'border-red-400 bg-red-50'; break;
                                        }
                                    }
                                @endphp
                                <div class="group relative flex flex-col items-center">
                                    @if($pc)
                                    @php
                                        $activeTask = $pc->activeTask;
                                        $lastTask = $pc->lastCompletedTask;
                                        $actionUrl = route('equipment.edit', $pc->id);
                                        // Default link logic if simple <a> wrapper
                                        if ($activeTask) {
                                            $actionUrl = route('tasks.checklist', $activeTask->id);
                                        } elseif ($lastTask) {
                                            $actionUrl = route('tasks.show', $lastTask->id);
                                        }
                                    @endphp
                                    <div class="relative w-20 h-20">
                                        <a href="{{ $actionUrl }}" class="block w-full h-full rounded-xl shadow-sm border-2 {{ $borderClass }} flex items-center justify-center p-2 cursor-pointer hover:scale-105 transition-transform hover:ring-2 hover:ring-indigo-400" title="{{ $pc->inventory_code }}">
                                            <img src="{{ asset('img/pc_icon.png') }}" alt="PC" class="w-full h-full object-contain opacity-90">
                                            
                                            <!-- Overlay Text specific for this context -->
                                            @if($activeTask)
                                                <div class="absolute inset-0 flex items-center justify-center bg-amber-500/80 text-white rounded-xl font-bold text-[10px] opacity-0 hover:opacity-100 transition-opacity">CONTINUAR</div>
                                            @elseif($lastTask)
                                                <div class="absolute inset-0 flex items-center justify-center bg-indigo-600/80 text-white rounded-xl font-bold text-[10px] opacity-0 hover:opacity-100 transition-opacity">REPORTE</div>
                                            @else
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-xl font-bold text-[10px] opacity-0 hover:opacity-100 transition-opacity">EDITAR</div>
                                            @endif
                                        </a>

                                        @if($activeTask)
                                            <div class="absolute -top-3 -left-2 bg-amber-500 text-white rounded-full p-1 shadow-md border-2 border-white z-20" title="En Mantenimiento">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </div>
                                        @elseif($pc->status == 'faulty')
                                            <div class="absolute -top-3 -left-2 bg-red-500 text-white rounded-full p-1 shadow-md border-2 border-white z-20" title="Falla Reportada">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            </div>
                                        @elseif($lastTask)
                                            <div class="absolute -top-3 -left-2 bg-green-500 text-white rounded-full p-1 shadow-md border-2 border-white z-20" title="Mantenimiento Completado">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        @else
                                             <div class="absolute -top-3 -left-2 bg-gray-400 text-white rounded-full p-1 shadow-md border-2 border-white z-20" title="Sin Historial">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </div>
                                        @endif

                                        <!-- Corner Edit Pencil -->
                                        <a href="{{ route('equipment.edit', $pc->id) }}" class="absolute -top-2 -right-2 p-1 bg-white rounded-full text-gray-400 hover:text-indigo-600 shadow-md border border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity z-20" title="Editar Propiedades">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                    </div>
                                    @else
                                    <div class="w-20 h-20 rounded-xl shadow-sm border-2 {{ $borderClass }} flex items-center justify-center p-2">
                                        <span class="text-xs text-gray-300 font-bold">VACÍO</span>
                                    </div>
                                    @endif
                                    <span class="mt-2 text-xs font-bold text-slate-600 bg-white px-2 py-0.5 rounded shadow-sm">
                                        {{ $pc ? $pc->inventory_code : 'PC-' . str_pad($pcNum, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            @endfor
                        </div>
                    @endfor
                </div>

                <!-- PASILLO CENTRAL (Aisle) -->
                <div class="w-24 flex flex-col items-center justify-center border-x-2 border-dashed border-slate-300 mx-4 bg-slate-50/50">
                    <span class="text-slate-300 font-black text-4xl uppercase tracking-widest writing-vertical-lr transform rotate-90 py-10">PASILLO</span>
                </div>

                <!-- ALA DERECHA (Right Wing) -->
                 <div class="flex flex-col gap-6">
                    <h4 class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Ala Derecha</h4>
                    @for ($row = 1; $row <= $room->rows_right; $row++)
                        <div class="flex gap-4">
                            @for ($col = 3; $col >= 1; $col--)
                                @php 
                                    $pcNum = ($row - 1) * 6 + $col;
                                    $pc = $room->equipments->firstWhere('position_index', $pcNum);
                                     // Determinar clases según estado
                                    $borderClass = 'border-gray-300 bg-white'; 
                                    if ($pc) {
                                        switch($pc->status) {
                                            case 'operational': $borderClass = 'border-green-400 bg-green-50'; break;
                                            case 'maintenance': $borderClass = 'border-amber-400 bg-amber-50'; break;
                                            case 'faulty': $borderClass = 'border-red-400 bg-red-50'; break;
                                        }
                                    }
                                @endphp
                                <div class="group relative flex flex-col items-center">
                                    @if($pc)
                                    @php
                                        $activeTask = $pc->activeTask;
                                        $lastTask = $pc->lastCompletedTask;
                                        $actionUrl = route('equipment.edit', $pc->id);
                                        // Default link logic if simple <a> wrapper
                                        if ($activeTask) {
                                            $actionUrl = route('tasks.checklist', $activeTask->id);
                                        } elseif ($lastTask) {
                                            $actionUrl = route('tasks.show', $lastTask->id);
                                        }
                                    @endphp
                                    <div class="relative w-20 h-20">
                                        <a href="{{ $actionUrl }}" class="block w-full h-full rounded-xl shadow-sm border-2 {{ $borderClass }} flex items-center justify-center p-2 cursor-pointer hover:scale-105 transition-transform hover:ring-2 hover:ring-indigo-400" title="{{ $pc->inventory_code }}">
                                            <img src="{{ asset('img/pc_icon.png') }}" alt="PC" class="w-full h-full object-contain opacity-90">
                                            
                                            <!-- Overlay Text specific for this context -->
                                            @if($activeTask)
                                                <div class="absolute inset-0 flex items-center justify-center bg-amber-500/80 text-white rounded-xl font-bold text-[10px] opacity-0 hover:opacity-100 transition-opacity">CONTINUAR</div>
                                            @elseif($lastTask)
                                                <div class="absolute inset-0 flex items-center justify-center bg-indigo-600/80 text-white rounded-xl font-bold text-[10px] opacity-0 hover:opacity-100 transition-opacity">REPORTE</div>
                                            @else
                                                <div class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-xl font-bold text-[10px] opacity-0 hover:opacity-100 transition-opacity">EDITAR</div>
                                            @endif
                                        </a>

                                        @if($activeTask)
                                            <div class="absolute -top-3 -left-2 bg-amber-500 text-white rounded-full p-1 shadow-md border-2 border-white z-20" title="En Mantenimiento">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </div>
                                        @elseif($pc->status == 'faulty')
                                            <div class="absolute -top-3 -left-2 bg-red-500 text-white rounded-full p-1 shadow-md border-2 border-white z-20" title="Falla Reportada">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            </div>
                                        @elseif($lastTask)
                                            <div class="absolute -top-3 -left-2 bg-green-500 text-white rounded-full p-1 shadow-md border-2 border-white z-20" title="Mantenimiento Completado">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        @else
                                             <div class="absolute -top-3 -left-2 bg-gray-400 text-white rounded-full p-1 shadow-md border-2 border-white z-20" title="Sin Historial">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </div>
                                        @endif

                                        <!-- Corner Edit Pencil -->
                                        <a href="{{ route('equipment.edit', $pc->id) }}" class="absolute -top-2 -right-2 p-1 bg-white rounded-full text-gray-400 hover:text-indigo-600 shadow-md border border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity z-20" title="Editar Propiedades">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                    </div>
                                    @else
                                    <div class="w-20 h-20 rounded-xl shadow-sm border-2 {{ $borderClass }} flex items-center justify-center p-2">
                                        <span class="text-xs text-gray-300 font-bold">VACÍO</span>
                                    </div>
                                    @endif
                                    <span class="mt-2 text-xs font-bold text-slate-600 bg-white px-2 py-0.5 rounded shadow-sm">
                                         {{ $pc ? $pc->inventory_code : 'PC-' . str_pad($pcNum, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            @endfor
                        </div>
                    @endfor
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
