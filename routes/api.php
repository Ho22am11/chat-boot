<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\OpenAiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/chat', [ChatController::class, 'chat']);
Route::post('/openai/chat', [OpenAiController::class, 'chat']);

Route::get('/models', [ChatController::class, 'listAvailableModels']);
