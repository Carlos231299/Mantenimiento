@extends('layouts.app')

@section('header', 'Salas de Cómputo')

@section('content')
<div class="space-y-6">
    <!-- Actions Bar -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-gray-700 font-medium">Listado de Salas</h3>
        <a href="{{ route('rooms.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center font-medium shadow-lg shadow-indigo-500/30">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nueva Sala
        </a>
    </div>

    <!-- Rooms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($rooms as $room)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
            <div class="h-32 bg-slate-800 flex items-center justify-center relative">
                <div class="absolute inset-0 bg-gradient-to-tr from-indigo-900 to-slate-800 opacity-90"></div>
                <!-- Icono de fondo decorativo -->
                <svg class="w-16 h-16 text-white/20 absolute -right-4 -bottom-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                
                <h4 class="text-2xl font-bold text-white relative z-10">{{ $room->name }}</h4>
            </div>
            <div class="p-5">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm text-gray-500">Capacidad</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $room->rows_left * 3 + $room->rows_right * 3 }} Espacios</span>
                </div>
                <!-- Información adicional -->
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-500">Ubicación</span>
                    <span class="text-xs text-gray-700 font-medium truncate max-w-[120px]" title="{{ $room->location }}">{{ $room->location }}</span>
                </div>
                <div class="flex justify-between items-center mb-6">
                    <span class="text-sm text-gray-500">Equipos</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $room->equipments_count }} Registrados</span>
                </div>
                
                <a href="{{ route('rooms.show', $room->id) }}" class="block w-full text-center px-4 py-2 border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors font-medium">
                    Ver Sala
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay salas creadas</h3>
            <p class="mt-1 text-sm text-gray-500">Comienza creando una nueva sala.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
