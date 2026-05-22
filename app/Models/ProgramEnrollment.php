<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'program_id', 'enrolled_at', 'status'])]
#[Table('program_enrollments')]

class ProgramEnrollment extends Model
{
 
    protected $casts = [
        'enrolled_at' => 'datetime',
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

    /**
     * Scope to get pending enrollments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
