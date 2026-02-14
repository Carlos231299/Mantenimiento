<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function create(Request $request)
    {
        $roomId = $request->query('room_id');
        return view('equipment.form', compact('roomId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_code' => 'required|string|unique:equipment,inventory_code',
            'room_id' => 'required|exists:rooms,id',
            'status' => 'required|in:operational,maintenance,faulty',
            'ip_address' => 'nullable|ipv4',
            'specifications' => 'nullable|string',
            'position_index' => 'nullable|integer',
            'is_teacher_pc' => 'nullable|boolean',
        ]);

        Equipment::create($validated);
        return redirect()->route('rooms.show', $request->room_id);
    }

    public function edit($id)
    {
        $equipment = Equipment::findOrFail($id);
        return view('equipment.form', compact('equipment'));
    }

    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        
        $validated = $request->validate([
            'inventory_code' => 'required|string|unique:equipment,inventory_code,'.$id,
            'status' => 'required|in:operational,maintenance,faulty',
            'ip_address' => 'nullable|string', // Cambiado a string por si acaso
            'specifications' => 'nullable|string',
            'position_index' => 'nullable|integer',
            'is_teacher_pc' => 'nullable|boolean',
            'findings' => 'nullable|array',
            'findings.*' => 'string'
        ]);

        $oldStatus = $equipment->status;
        $equipment->update($validated);

        // 1. Sincronizar Hallazgos con Tarea Activa (Si existe)
        $activeTask = $equipment->activeTask;
        if ($activeTask) {
            $checklistData = $activeTask->checklist_data ?? [];
            if ($request->has('findings')) {
                $checklistData['preliminary_findings'] = $request->findings;
            } else {
                // Si enviaron el form pero sin hallazgos, vaciarlos (según lógica del form)
                $checklistData['preliminary_findings'] = [];
            }
            $activeTask->update(['checklist_data' => $checklistData]);
        }

        // 2. Lógica de Negocio: Generar Tarea Automática
        // Si cambia a Falla o Mantenimiento y no tiene tarea activa
        if (in_array($request->status, ['faulty', 'maintenance']) && $oldStatus == 'operational') {
            
            if (!$activeTask) {
                // Guardar hallazgos en checklist_data para separarlos de las observaciones de mantenimiento
                $checklistData = [];
                if ($request->has('findings') && count($request->findings) > 0) {
                    $checklistData['preliminary_findings'] = $request->findings;
                }

                \App\Models\Task::create([
                    'equipment_id' => $equipment->id,
                    'status' => 'pending',
                    'priority' => ($request->status == 'faulty') ? 'high' : 'normal',
                    'checklist_data' => $checklistData,
                    'observations' => null, 
                    'created_at' => now(),
                ]);
            }
        }

        return redirect()->route('rooms.show', $equipment->room_id);
    }
}
