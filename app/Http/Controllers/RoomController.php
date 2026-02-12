<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::withCount('equipments')->get();
        return view('rooms.index', compact('rooms'));
    }

    public function show($id)
    {
        $room = Room::with(['equipments' => function($query) {
            $query->with(['activeTask', 'lastCompletedTask']);
        }])->findOrFail($id);
        
        // Organizar equipos por inventario o posición para fácil acceso en la vista
        // Aunque la vista actual usa lógica de fors, podemos pasar la colección.
        
        return view('rooms.show', compact('room'));
    }

    public function create()
    {
        return view('rooms.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'rows_left' => 'required|integer|min:0|max:10',
            'rows_right' => 'required|integer|min:0|max:10',
            'capacity' => 'required|integer|min:1',
            'admin_capacity' => 'required|integer|min:1',
        ]);

        $room = Room::create($request->all());
        $roomInitials = strtoupper(substr($room->name, 0, 3));
        
        // 1. Crear PCs del Profesor/Administrador
        $adminCapacity = $request->input('admin_capacity');
        for ($i = 1; $i <= $adminCapacity; $i++) {
            $suffix = $adminCapacity > 1 ? "-{$i}" : ""; // Pone PROF-1 si hay varios, solo PROF si es 1
            \App\Models\Equipment::create([
                'room_id' => $room->id,
                'inventory_code' => "{$roomInitials}-{$room->id}-PROF{$suffix}",
                'status' => 'operational',
                'is_teacher_pc' => true,
                'specifications' => 'PC Profesor / Admin',
                'position_index' => -1 * $i, // Índices negativos para admins
            ]);
        }

        // 2. Generar equipos de estudiantes según capacidad exacta
        $studentPcsCreated = 0;
        $totalCapacity = $request->input('capacity');
        $maxRows = max($room->rows_left, $room->rows_right);

        for ($row = 1; $row <= $maxRows; $row++) {
            
            // A. ALA DERECHA (Índices 1, 2, 3... relativos a la fila)
            // Se llena PRIMERO
            if ($row <= $room->rows_right) {
                for ($col = 1; $col <= 3; $col++) {
                    if ($studentPcsCreated >= $totalCapacity) break 2;

                    $code = "{$roomInitials}-{$room->id}-R{$row}-C{$col}";
                    $posIndex = ($row - 1) * 6 + $col; // 1, 2, 3...
                    
                    \App\Models\Equipment::create([
                        'room_id' => $room->id,
                        'inventory_code' => $code,
                        'status' => 'operational',
                        'position_index' => $posIndex,
                        'specifications' => 'Equipo Estudiante',
                    ]);
                    $studentPcsCreated++;
                }
            }

            // B. ALA IZQUIERDA (Índices 4, 5, 6... relativos a la fila)
            // Se llena SEGUNDO
            if ($row <= $room->rows_left) {
                for ($col = 1; $col <= 3; $col++) {
                    if ($studentPcsCreated >= $totalCapacity) break 2; 

                    $code = "{$roomInitials}-{$room->id}-L{$row}-C{$col}";
                    $posIndex = ($row - 1) * 6 + ($col + 3); // 4, 5, 6...
                    
                    \App\Models\Equipment::create([
                        'room_id' => $room->id,
                        'inventory_code' => $code,
                        'status' => 'operational',
                        'position_index' => $posIndex,
                        'specifications' => 'Equipo Estudiante',
                    ]);
                    $studentPcsCreated++;
                }
            }
        }

        return redirect()->route('rooms.show', $room->id)->with('success', "Sala creada con {$adminCapacity} Admin(s) y {$studentPcsCreated} Estudiantes.");
    }
    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        
        // Eliminar equipos y sus tareas asociadas (si config DB cascade no está activo)
        foreach ($room->equipments as $equipment) {
            $equipment->tasks()->delete();
            $equipment->delete();
        }
        
        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Sala eliminada correctamente.');
    }
}
