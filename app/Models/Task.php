<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'technician_id',
        'status',
        'priority',
        'checklist_data',
        'observations',
        'due_date',
        'completed_at',
    ];

    protected $casts = [
        'checklist_data' => 'array',
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
