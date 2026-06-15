<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlacementQuestion extends Model
{
    protected $fillable = [
        'section',
        'level',
        'question_text',
        'options',
        'correct_option',
        'explanation',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_option' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
