@extends('layouts.app')

@section('header', 'Panel de Control')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 rounded-xl bg-blue-50 text-blue-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Salas Registradas</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalRooms }}</h3>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 rounded-xl bg-indigo-50 text-indigo-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Equipos Totales</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalEquipment }}</h3>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
            <div class="p-3 rounded-xl bg-amber-50 text-amber-600 mr-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Mantenimientos Pendientes</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $pendingMaintenance }}</h3>
            </div>
        </div>
    </div>

    <!-- Recent Activity / Content Placeholder -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Actividad Reciente (Tareas)</h3>
        <div class="space-y-4">
            @forelse($recentActivity as $activity)
                <div class="flex items-start pb-4 border-b border-gray-50 last:border-0 last:pb-0">
                    <div class="w-2 h-2 mt-2 rounded-full {{ $activity->status == 'completed' ? 'bg-green-500' : 'bg-amber-500' }} mr-3"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            {{ $activity->status == 'completed' ? 'Mantenimiento Completado' : 'Ticket Generado / En Proceso' }} 
                            - {{ $activity->equipment->inventory_code ?? 'Equipo ???' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $activity->updated_at->diffForHumans() }} 
                            @if($activity->technician) por {{ $activity->technician->name }} @endif
                            ({{ $activity->equipment->room->name ?? 'Sin Sala' }})
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 italic">No hay actividad reciente registrada.</p>
            @endforelse
        </div>
    </div>
@endsection
