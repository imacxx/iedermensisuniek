<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\UploadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// API routes for pages (accessible to authenticated users)
Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/pages/{slug}', [PageController::class, 'show']);
    Route::put('/pages/{slug}', [PageController::class, 'update']);
});

// API route for uploads (accessible to authenticated users)
Route::post('/uploads', [UploadController::class, 'store'])->middleware('auth:sanctum');

// Block schema endpoint (public - just metadata)
Route::get('/blocks/schema', function () {
    return response()->json(\App\Services\BlockSchema::getAvailableBlocks());
});
