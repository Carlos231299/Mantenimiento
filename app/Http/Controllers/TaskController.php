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

        return view('tasks.index', compact('tasks', 'pending', 'inProgress', 'completed', 'rooms', 'roomId'));
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
            
            // 1. Detectar patrón antiguo: "Hallazgos Preliminares:\n- item..."
            if (str_contains($task->observations, 'Hallazgos Preliminares:')) {
                // Extraer el texto de los hallazgos
                $parts = explode('Hallazgos Preliminares:', $task->observations);
                $findingsText = $parts[1] ?? '';
                
                // Convertir líneas "- item" en array
                $lines = explode("\n", $findingsText);
                $findings = [];
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (str_starts_with($line, '- ')) {
                        $findings[] = substr($line, 2);
                    } elseif (!empty($line)) {
                        $findings[] = $line;
                    }
                }

                // Inyectar en checklist_data (en memoria, para la vista)
                $data = $task->checklist_data ?? [];
                $data['preliminary_findings'] = $findings;
                $task->checklist_data = $data;

                // Limpiar observaciones para que el textarea aparezca vacío o limpio
                // Quitamos el header "Reporte automático..." y los hallazgos.
                // Si había notas extra del admin, intentar conservarlas (difícil saber qué es qué), 
                // pero por seguridad, si es el texto default, lo dejamos vacío.
                if (str_contains($task->observations, 'Reporte automático desde Inspección de Sala')) {
                     $task->observations = null; 
                }

                // GUARDAR CAMBIOS: Es crucial guardar para que la migración sea permanente
                // y esté disponible cuando se llame al método update() y para futuras visualizaciones.
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
            // Preserve existing preliminary findings if not present in request (they are usually not sent back if not editable, but we should be careful)
            // Actually, usually we want to merge with existing DB data or ensure the view sends everything.
            // Simplified: The view will send everything in checklist_data array.
            // Preliminary findings might need to be preserved if the view doesn't send them back.
            // Let's grab existing to be safe.
            $existingData = $task->checklist_data ?? [];
            if (isset($existingData['preliminary_findings'])) {
                $data['preliminary_findings'] = $existingData['preliminary_findings'];
            }

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
        $existingData = $task->checklist_data ?? [];
        if (isset($existingData['preliminary_findings'])) {
            $data['preliminary_findings'] = $existingData['preliminary_findings'];
        }

        $task->update([
            'status' => 'completed',
            'checklist_data' => $data,
            'completed_at' => now(),
            'observations' => $request->observations,
            'is_urgent' => $request->has('is_urgent'), 
        ]);

        // Lógica de Negocio: Al completar tarea, equipo -> operativo
        if ($task->equipment) {
            $task->equipment->update(['status' => 'operational']);
        }

        return redirect()->route('tasks.index');
    }
}
