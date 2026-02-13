<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- AUTENTICACIÓN ---
Route::middleware('guest')->group(function () {
    Route::get('/', function () { return redirect()->route('login'); });
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// --- RUTAS PROTEGIDAS ---
Route::middleware('auth')->group(function () {
    
    Route::get('/dashboard', function () {
        // KPIs Reales
        $totalRooms = \App\Models\Room::count();
        $totalEquipment = \App\Models\Equipment::count();
        $pendingMaintenance = \App\Models\Task::where('status', '!=', 'completed')->count();

        // Actividad Reciente (Últimas 5 tareas)
        $recentActivity = \App\Models\Task::with(['equipment.room', 'technician'])
                                          ->latest()
                                          ->take(5)
                                          ->get();

        return view('dashboard', compact('totalRooms', 'totalEquipment', 'pendingMaintenance', 'recentActivity'));
    })->name('dashboard');

    // REPORTES (Admin y Técnicos)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/preliminary', [ReportController::class, 'preliminary'])->name('reports.preliminary');

    // TAREAS (Admin y Técnicos)
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{id}/checklist', [TaskController::class, 'checklist'])->name('tasks.checklist');
    Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');

    // SALAS y EQUIPOS (Solo Admin)
    Route::middleware('role:admin')->group(function () {
        // Salas
        Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
        Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::get('/rooms/{id}', [RoomController::class, 'show'])->name('rooms.show');
        Route::post('/rooms/{id}/reorder', [RoomController::class, 'reorderEquipment'])->name('rooms.reorder');
        Route::delete('/rooms/{id}', [RoomController::class, 'destroy'])->name('rooms.destroy');

        // Equipos
        Route::get('/equipment/create', [EquipmentController::class, 'create'])->name('equipment.create');
        Route::post('/equipment', [EquipmentController::class, 'store'])->name('equipment.store');
        Route::get('/equipment/{id}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
        Route::put('/equipment/{id}', [EquipmentController::class, 'update'])->name('equipment.update');
        
        // Usuarios (Solo Admin)
        Route::get('/users', function () { 
            $users = \App\Models\User::all();
            return view('users.index', compact('users')); 
        })->name('users.index');
        Route::get('/users/create', function () { return view('users.form'); })->name('users.create');
        Route::get('/users/1/edit', function () { return view('users.form', ['user' => true]); })->name('users.edit');
    });
});
