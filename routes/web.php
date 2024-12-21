<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ListController;

Route::get('/', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::post('/tasks/{task}', [TaskController::class, 'update']);
Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

Route::post('/tasks/{task}/lists', [ListController::class, 'store']);
Route::post('/tasks/{task}/lists/{list}', [ListController::class, 'update']);
Route::delete('/tasks/{task}/lists/{list}', [ListController::class, 'destroy']);
