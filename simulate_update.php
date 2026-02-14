<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Equipment;
use Illuminate\Http\Request;

$id = 71; // SAL-3-I2-C1
$equipment = Equipment::find($id);

echo "Simulating update for " . $equipment->inventory_code . PHP_EOL;

$data = [
    'inventory_code' => 'SAL-3-I2-C1',
    'status' => 'maintenance',
    'ip_address' => null,
    'specifications' => $equipment->specifications,
    'position_index' => $equipment->position_index,
    'is_teacher_pc' => $equipment->is_teacher_pc,
    'findings' => ['Test finding']
];

try {
    $oldStatus = $equipment->status;
    echo "Old Status: $oldStatus" . PHP_EOL;
    echo "New Status: " . $data['status'] . PHP_EOL;
    
    $equipment->update($data);
    echo "Equipment updated successfully." . PHP_EOL;

    $activeTask = $equipment->activeTask;
    echo "Active Task after update: " . ($activeTask ? "YES (ID: ".$activeTask->id.")" : "NO") . PHP_EOL;

    if (in_array($data['status'], ['faulty', 'maintenance']) && $oldStatus == 'operational') {
        if (!$activeTask) {
            echo "Attempting to create Task..." . PHP_EOL;
            $task = \App\Models\Task::create([
                'equipment_id' => $equipment->id,
                'status' => 'pending',
                'priority' => ($data['status'] == 'faulty') ? 'high' : 'normal',
                'checklist_data' => ['preliminary_findings' => $data['findings']],
                'observations' => null,
                'created_at' => now(),
            ]);
            echo "Task created successfully. ID: " . $task->id . PHP_EOL;
        } else {
            echo "Active task already exists, skipping creation." . PHP_EOL;
        }
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
