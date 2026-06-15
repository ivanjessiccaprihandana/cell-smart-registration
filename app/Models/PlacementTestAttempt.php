<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlacementTestAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'total_questions',
        'correct_answers',
        'score_percentage',
        'level',
        'recommended_program',
        'answers',
        'duration_seconds',
    ];

    protected $casts = [
        'answers' => 'array',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'score_percentage' => 'integer',
        'duration_seconds' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
