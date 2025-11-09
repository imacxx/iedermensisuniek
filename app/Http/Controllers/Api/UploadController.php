<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Store a newly uploaded file.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('uploads', 'public');

            return response()->json([
                'url' => Storage::url($path),
                'path' => $path,
            ], 201);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
