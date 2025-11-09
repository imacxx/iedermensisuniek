<?php

namespace App\Models;

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
    ];

    protected $casts = [
        'blocks' => 'array',
    ];
}
