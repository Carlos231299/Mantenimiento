<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'inventory_code',
        'ip_address',
        'specifications',
        'status',
        'is_teacher_pc',
        'position_index',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function activeTask()
    {
        return $this->hasOne(Task::class)->whereIn('status', ['pending', 'in_progress'])->latestOfMany();
    }

    public function lastCompletedTask()
    {
        return $this->hasOne(Task::class)->where('status', 'completed')->latestOfMany();
    }
}
