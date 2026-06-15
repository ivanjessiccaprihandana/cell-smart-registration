<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class HomeClass extends Model
{
    protected $fillable = [
        'key',
        'title',
        'badge',
        'description',
        'heading',
        'heading_suffix',
        'quota_program_name',
        'quota_label',
        'features',
        'modal_title',
        'modal_description',
        'modal_breadcrumbs',
        'sub_programs',
        'grid_columns',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'modal_breadcrumbs' => 'array',
        'sub_programs' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function modalId(): string
    {
        return str($this->key)->camel()->toString() . 'Modal';
    }
}
