<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('program_enrollments')]

class ProgramEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'program_id',
        'class_type',
        'type',
        'enrolled_at',
        'start_date',
        'end_date',
        'status',
    ];
 
    protected $casts = [
        'enrolled_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the user associated with this enrollment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the program associated with this enrollment
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Scope to get active enrollments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrent($query)
    {
        return $query
            ->whereIn('status', ['pending', 'active'])
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now()->toDateString());
            });
    }

    /**
     * Scope to get pending enrollments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
