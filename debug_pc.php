<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Equipment;

$code = 'SAL-3-I2-C1';
$pc = Equipment::where('inventory_code', $code)->first();

if ($pc) {
    echo "ID: " . $pc->id . PHP_EOL;
    echo "Inventory Code: '" . $pc->inventory_code . "'" . PHP_EOL;
    echo "Status: " . $pc->status . PHP_EOL;
    echo "Room ID: " . $pc->room_id . PHP_EOL;
    echo "Active Task: " . ($pc->activeTask ? "YES (ID: " . $pc->activeTask->id . ", STATUS: " . $pc->activeTask->status . ")" : "NO") . PHP_EOL;
    
    // Check for duplicates
    $duplicates = Equipment::where('inventory_code', $code)->where('id', '!=', $pc->id)->count();
    echo "Duplicates: " . $duplicates . PHP_EOL;
} else {
    echo "PC NOT FOUND with exact code '" . $code . "'" . PHP_EOL;
    
    // Fuzzy search
    $fuzzy = Equipment::where('inventory_code', 'LIKE', '%'.$code.'%')->get();
    echo "Fuzzy matches: " . $fuzzy->count() . PHP_EOL;
    foreach($fuzzy as $f) {
        echo " - ID: " . $f->id . " CODE: '" . $f->inventory_code . "'" . PHP_EOL;
    }
}
