<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Room;
use App\Models\Equipment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usuario Único (Admin)
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@system.com',
            'password' => Hash::make('password'),
            'role' => 'admin', // Aunque sea rol único, mantenemos 'admin' por compatibilidad
        ]);

        // Sin salas ni equipos ni tareas iniciales.
        // El sistema arranca limpio.
    }
}
