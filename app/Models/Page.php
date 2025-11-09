<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'blocks',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_published',
    ];

    protected $casts = [
        'blocks' => 'array',
        'is_published' => 'boolean',
    ];

    /**
     * Scope pages that are published.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
