<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Rutas de la API (CRUD)
Route::apiResource('tareas', TaskController::class);

// Funcionalidad adicional: Resumen
Route::get('tareas/resumen', [TaskController::class, 'resumen']);