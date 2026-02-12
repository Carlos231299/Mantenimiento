@extends('layouts.app')

@section('header', isset($user) ? 'Editar Usuario' : 'Crear Usuario')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Información del Usuario</h3>
            <a href="{{ route('users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">Volver al listado</a>
        </div>
        
        <form action="{{ route('users.index') }}" method="GET" class="p-6 space-y-6">
            <!-- Simulated Form - Action redirects back to index for visual flow -->
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="col-span-2 sm:col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                    <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3" placeholder="Ej. Juan Pérez" value="{{ isset($user) ? 'Carlos Admin' : '' }}">
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3" placeholder="ejemplo@correo.com" value="{{ isset($user) ? 'carlos@admin.com' : '' }}">
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                    <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3">
                        <option>Administrador</option>
                        <option>Técnico</option>
                        <option>Supervisor</option>
                    </select>
                </div>

                <div class="col-span-2 sm:col-span-1">
                    <label for="status" class="block text-sm font-medium text-gray-700">Estado</label>
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3">
                        <option>Activo</option>
                        <option>Inactivo</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 border px-3" placeholder="••••••••">
                    <p class="mt-1 text-xs text-gray-500">Dejar en blanco para mantener la actual (solo edición).</p>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end border-t border-gray-100 mt-6">
                <button type="button" class="mr-3 text-sm text-gray-600 hover:text-gray-900 font-medium">Cancelar</button>
                <button type="submit" class="bg-indigo-600 border border-transparent rounded-lg shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-indigo-500/20">
                    Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
