<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// 1. Ruta Principal: Dirige '/' a la vista de login (o a la vista de tareas si ya está autenticado)
Route::get('/', function () {
    // Si el usuario NO está autenticado, muestra la vista de login.
    if (!auth()->check()) {
        return view('login'); 
    }
    // Si el usuario está autenticado, lo redirige al índice de tareas.
    return redirect()->route('tasks.index'); 
})->name('home'); 

// 2. Rutas de Autenticación Social (Sin middleware 'auth')
Route::prefix('auth')->group(function () {
    Route::get('/{provider}', [App\Http\Controllers\SocialController::class, 'redirect'])->name('social.redirect');
    Route::get('/{provider}/callback', [App\Http\Controllers\SocialController::class, 'callback'])->name('social.callback');
});

// 3. Rutas de Gestión de Tareas (Protegidas por middleware 'auth')
// Solo se puede acceder a estas rutas después de iniciar sesión.
Route::middleware(['auth'])->group(function () {
    Route::get('/tareas', [TaskController::class, 'indexWeb'])->name('tasks.index');
    Route::get('/tareas/{id}/editar', function ($id) {
        return view('tasks.edit', ['taskId' => $id]);
    })->name('tasks.edit');
    // Si necesitas una ruta de logout
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home'); // Vuelve a la página de inicio (login)
    })->name('logout');
});

// Nota: Elimina la ruta '/login' si ya no la necesitas, ya que '/' la cubre.