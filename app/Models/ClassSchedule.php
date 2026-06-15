<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSchedule extends Model
{
    protected $fillable = [
        'program_id',
        'class_type',
        'class_date',
        'session_name',
        'start_time',
        'end_time',
        'room',
        'notes',
    ];

    protected $casts = [
        'class_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
