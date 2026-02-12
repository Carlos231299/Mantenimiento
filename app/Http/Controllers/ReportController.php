<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Task;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $roomId = $request->input('room_id');
        $rooms = Room::all();

        // 1. Resumen General
        $totalRooms = Room::count(); // Total global, irrelevante filtrar
        
        // Total Equipment (Filtrado)
        $eqQuery = Equipment::query();
        if ($roomId) $eqQuery->where('room_id', $roomId);
        $totalEquipment = $eqQuery->count();

        // Faulty Equipment (Filtrado)
        $faultyQuery = Equipment::whereIn('status', ['faulty', 'maintenance']);
        if ($roomId) $faultyQuery->where('room_id', $roomId);
        $faultyEquipment = $faultyQuery->count();

        // Pending Tasks (Filtrado por relacion equipment->room)
        $taskQuery = Task::where('status', 'pending');
        if ($roomId) {
            $taskQuery->whereHas('equipment', function($q) use ($roomId) {
                $q->where('room_id', $roomId);
            });
        }
        $pendingTasks = $taskQuery->count();

        // 2. Equipos con más fallas (Top 5)
        // Se asume que cada tarea representa una falla/mantenimiento
        $topFaultyQuery = Equipment::withCount('tasks');
        if ($roomId) $topFaultyQuery->where('room_id', $roomId);
        
        $topFaultyEquipment = $topFaultyQuery->orderByDesc('tasks_count')
            ->take(5)
            ->get();

        // 3. Tareas completadas recientemente (Último mes)
        $completedQuery = Task::where('status', 'completed')
            ->where('completed_at', '>=', Carbon::now()->subMonth())
            ->with(['equipment.room', 'technician']);
            
        if ($roomId) {
            $completedQuery->whereHas('equipment', function($q) use ($roomId) {
                $q->where('room_id', $roomId);
            });
        }
        
        $completedTasks = $completedQuery->orderByDesc('completed_at')
            ->get();

        return view('reports.index', compact(
            'totalRooms', 
            'totalEquipment', 
            'faultyEquipment', 
            'pendingTasks',
            'topFaultyEquipment',
            'completedTasks',
            'rooms',
            'roomId'
        ));
    }

    public function print(Request $request)
    {
        // Misma lógica pero orientada a vista de impresión
        // Podríamos filtrar por fecha si el request lo pide
        $date = Carbon::now();
        
        $completedTasks = Task::where('status', 'completed')
             ->with(['equipment.room', 'technician'])
             ->orderByDesc('completed_at')
             ->get();

        return view('reports.print', compact('completedTasks', 'date'));
    }

    public function preliminary(Request $request)
    {
        $roomId = $request->query('room_id');
        
        if (!$roomId) {
            return redirect()->route('reports.index')->with('error', 'Seleccione una sala para generar la propuesta.');
        }

        $room = Room::findOrFail($roomId);
        $date = Carbon::now();

        // Equipos con problemas
        $faultyEquipment = Equipment::where('room_id', $roomId)
            ->whereIn('status', ['faulty', 'maintenance'])
            ->with(['tasks' => function($q) {
                $q->where('status', '!=', 'completed');
            }])
            ->get();

        // Todas las tareas pendientes de la sala
        $pendingTasks = Task::whereHas('equipment', function ($q) use ($roomId) {
            $q->where('room_id', $roomId);
        })->where('status', 'pending')
          ->with('equipment')
          ->get();

        return view('reports.preliminary', compact('room', 'faultyEquipment', 'pendingTasks', 'date'));
    }
}
