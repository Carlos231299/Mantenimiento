<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'rows_left',
        'rows_right',
    ];

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}
