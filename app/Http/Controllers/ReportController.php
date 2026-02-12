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
        $rooms = Room::withCount('equipments')->get();

        // 1. Resumen General
        $totalRooms = Room::count(); 
        
        // Total Equipment (Filtrado)
        $eqQuery = Equipment::query();
        if ($roomId) $eqQuery->where('room_id', $roomId);
        $totalEquipment = $eqQuery->count();

        // Operativo Equipment (Para Índice de Salud)
        $operationalQuery = Equipment::where('status', 'operational');
        if ($roomId) $operationalQuery->where('room_id', $roomId);
        $operationalEquipment = $operationalQuery->count();

        // Índice de Salud (Porcentaje)
        $healthIndex = ($totalEquipment > 0) ? round(($operationalEquipment / $totalEquipment) * 100) : 0;

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

        // 2. Distribución de Prioridades
        $priorityDistribution = Task::where('status', 'pending')
            ->selectRaw('priority, count(*) as count')
            ->groupBy('priority');
        
        if ($roomId) {
            $priorityDistribution->whereHas('equipment', function($q) use ($roomId) {
                $q->where('room_id', $roomId);
            });
        }
        $priorityStats = $priorityDistribution->pluck('count', 'priority')->toArray();

        // 3. Ranking de Salud por Sala (Solo si no hay filtro de sala específico)
        $roomHealthRanking = [];
        if (!$roomId) {
            foreach ($rooms as $room) {
                $total = $room->equipments_count;
                $op = Equipment::where('room_id', $room->id)->where('status', 'operational')->count();
                $roomHealthRanking[] = [
                    'name' => $room->name,
                    'health' => ($total > 0) ? round(($op / $total) * 100) : 0,
                    'total' => $total,
                    'faulty' => $total - $op
                ];
            }
            // Ordenar por salud ascendente (salas con más problemas primero)
            usort($roomHealthRanking, fn($a, $b) => $a['health'] <=> $b['health']);
        }

        // 4. Equipos con más fallas (Top 5)
        $topFaultyQuery = Equipment::withCount('tasks');
        if ($roomId) $topFaultyQuery->where('room_id', $roomId);
        
        $topFaultyEquipment = $topFaultyQuery->orderByDesc('tasks_count')
            ->take(5)
            ->get();

        // 5. Tareas completadas recientemente
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
            'healthIndex',
            'priorityStats',
            'roomHealthRanking',
            'topFaultyEquipment',
            'completedTasks',
            'rooms',
            'roomId'
        ));
    }

    public function print(Request $request)
    {
        $date = Carbon::now();
        $rooms = Room::withCount('equipments')->get();
        
        // 1. Métricas Globales
        $totalRooms = Room::count();
        $totalEquipment = Equipment::count();
        $operationalEquipment = Equipment::where('status', 'operational')->count();
        $faultyEquipment = Equipment::whereIn('status', ['faulty', 'maintenance'])->count();
        $pendingTasksCount = Task::where('status', 'pending')->count();
        $healthIndex = ($totalEquipment > 0) ? round(($operationalEquipment / $totalEquipment) * 100) : 0;

        // 2. Prioridades
        $priorityStats = Task::where('status', 'pending')
            ->selectRaw('priority, count(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')->toArray();

        // 3. Salud por Sala
        $roomHealthRanking = [];
        foreach ($rooms as $room) {
            $total = $room->equipments_count;
            $op = Equipment::where('room_id', $room->id)->where('status', 'operational')->count();
            $roomHealthRanking[] = [
                'name' => $room->name,
                'health' => ($total > 0) ? round(($op / $total) * 100) : 0,
                'total' => $total,
                'faulty' => $total - $op
            ];
        }
        usort($roomHealthRanking, fn($a, $b) => $a['health'] <=> $b['health']);

        // 4. Mantenimientos (Último mes)
        $completedTasks = Task::where('status', 'completed')
             ->where('completed_at', '>=', Carbon::now()->subMonth())
             ->with(['equipment.room', 'technician'])
             ->orderByDesc('completed_at')
             ->get();

        return view('reports.print', compact(
            'date', 
            'totalRooms',
            'totalEquipment',
            'faultyEquipment',
            'pendingTasksCount',
            'healthIndex',
            'priorityStats',
            'roomHealthRanking',
            'completedTasks'
        ));
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
