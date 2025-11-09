<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    /**
     * Display the specified page by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        $this->authorize('view', $page);

        return response()->json([
            'title' => $page->title,
            'slug' => $page->slug,
            'blocks' => $page->blocks,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keywords' => $page->meta_keywords,
        ]);
    }

    /**
     * Update the specified page by slug.
     */
    public function update(Request $request, string $slug): JsonResponse
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        $this->authorize('update', $page);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'blocks' => 'sometimes|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $page->update($validated);

        return response()->json([
            'title' => $page->title,
            'slug' => $page->slug,
            'blocks' => $page->blocks,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keywords' => $page->meta_keywords,
        ]);
    }
}
