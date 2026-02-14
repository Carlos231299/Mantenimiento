<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $roomId = $request->input('room_id');
        $rooms = \App\Models\Room::all();

        // Obtener tareas con relaciones y filtro opcional
        $query = Task::with(['equipment.room', 'technician'])->orderByDesc('created_at');
        
        if ($roomId) {
            $query->whereHas('equipment', function ($q) use ($roomId) {
                $q->where('room_id', $roomId);
            });
        }

        $tasks = $query->get();
        
        // Contadores para KPI (respetan el filtro)
        $pending = $tasks->where('status', 'pending')->count();
        $inProgress = $tasks->where('status', 'in_progress')->count();
        $completed = $tasks->where('status', 'completed')->count();

        // Agrupar por sala para la vista principal
        $groupedTasks = $tasks->groupBy(function($task) {
            return $task->equipment->room->id;
        });

        return view('tasks.index', [
            'tasks' => $tasks, 
            'groupedTasks' => $groupedTasks, 
            'pending' => $pending, 
            'inProgress' => $inProgress, 
            'completed' => $completed, 
            'rooms' => $rooms, 
            'currentRoomId' => $roomId
        ]);
    }

    public function show($id)
    {
        $task = Task::with(['equipment.room', 'technician'])->findOrFail($id);
        return view('tasks.show', compact('task'));
    }

    public function checklist(Request $request, $id)
    {
        $task = Task::with(['equipment.room', 'technician'])->findOrFail($id);

        // Si la tarea está completada y no se ha confirmado la edición explícitamente, redirigir al reporte
        if ($task->status == 'completed' && !$request->has('edit_confirmed')) {
            return redirect()->route('tasks.show', $id);
        }

        // LÓGICA DE COMPATIBILIDAD (LEGACY TASKS)
        // Si no existe 'preliminary_findings' en checklist_data pero SÍ hay texto en observations
        // intentamos extraerlo para mostrarlo correctamente.
        if (!isset($task->checklist_data['preliminary_findings']) && $task->observations) {
            $lines = explode("\n", $task->observations);
            $findings = [];
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Si la línea empieza con "- " o "• ", remover el prefijo
                if (str_starts_with($line, '- ')) $line = substr($line, 2);
                if (str_starts_with($line, '• ')) $line = substr($line, 2);
                
                // Ignorar líneas del sistema
                if (str_contains($line, 'Reporte automático') || str_contains($line, 'Hallazgos Preliminares')) continue;
                
                $findings[] = $line;
            }

            if (!empty($findings)) {
                $data = $task->checklist_data ?? [];
                $data['preliminary_findings'] = $findings;
                $task->checklist_data = $data;
                // No limpiamos observations por si acaso, el técnico lo verá y podrá borrarlo manualmente si quiere
                $task->save();
            }
        }

        return view('tasks.checklist', compact('task'));
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        
        // 1. Guardado Parcial (Autosave)
        if ($request->has('save_progress')) {
            // Merge checklist data: preserve preliminary, add updates
            $data = $request->checklist_data ?? [];
            if ($request->has('maintenance_findings')) {
                $data['maintenance_findings'] = $request->maintenance_findings;
            }
            
            // Si la request trae preliminary_findings, usarlas. 
            // Si NO trae (campo vacío en el form), pondremos array vacío.
            // No preservamos de la DB porque el usuario puede haber borrado todos.
            $data['preliminary_findings'] = $request->input('checklist_data.preliminary_findings', []);

            $task->update([
                'checklist_data' => $data,
                'observations' => $request->observations, // Legacy text support (optional)
                'is_urgent' => $request->has('is_urgent'), 
            ]);
            
            // Si estaba pendiente, cambiar a 'en proceso' para indicar que ya se tocó
            if ($task->status == 'pending') {
                $task->update(['status' => 'in_progress']);
            }

            return redirect()->route('tasks.index')->with('success', 'Avance guardado exitosamente.');
        }

        // 2. Finalizar Mantenimiento
        $data = $request->checklist_data ?? [];
        if ($request->has('maintenance_findings')) {
            $data['maintenance_findings'] = $request->maintenance_findings;
        }
        $data['preliminary_findings'] = $request->input('checklist_data.preliminary_findings', []);

        $task->update([
            'status' => 'completed',
            'checklist_data' => $data,
            'completed_at' => now(),
            'observations' => $request->observations,
            'is_urgent' => $request->has('is_urgent'), 
        ]);

        // Lógica de Negocio: Al completar tarea, actualizar estado del equipo según lo indicado
        if ($task->equipment) {
            $finalStatus = $request->input('final_status', 'operational');
            $task->equipment->update(['status' => $finalStatus]);
        }

        return redirect()->route('rooms.show', $task->equipment->room_id)->with('success', 'Mantenimiento finalizado correctamente.');
    }
}
