<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'featured_image',
        'published_at',
        'is_published',
        'seo_title',
        'seo_description',
        'meta_keywords',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_published' => 'boolean',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'blog_category');
    }
}
