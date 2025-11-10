<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    // C - CREATE
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:pendiente,en progreso,completada',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    // R - READ (Listar y Filtrar)
public function indexWeb(Request $request)
{
    $query = Task::query();

    $estado = $request->input('estado'); // Captura el valor del filtro de estado
    $searchQuery = $request->input('q'); // 🎯 NUEVA LÍNEA: Captura el término de búsqueda

    // 1. Aplicar filtro por ESTADO
    if ($estado && $estado !== '') { 
        $query->where('estado', $estado);
    }
    
    // 2. 🎯 Aplicar filtro de BÚSQUEDA (en Título o Descripción)
    if ($searchQuery) {
        $query->where(function ($q) use ($searchQuery) {
            // Utilizamos una expresión regular (RegEx) de MongoDB para búsquedas flexibles
            // 'i' hace que la búsqueda sea insensible a mayúsculas/minúsculas.
            
            // Búsqueda en el Título
            $q->where('titulo', 'like', '%' . $searchQuery . '%');
            
            // O Búsqueda en la Descripción
            $q->orWhere('descripcion', 'like', '%' . $searchQuery . '%');
        });
    }

    // Ejecuta la consulta
    $tasks = $query->get(); 

    // Pasa las tareas a la vista
    return view('tasks.index', [
        'tasks' => $tasks,
        // No necesitamos pasar 'selected_estado' y 'q' explícitamente, 
        // ya que la función request() de Blade los recupera de la URL.
    ]);
}

    // R - READ (Detalle para Edición)
    public function show($id) 
    {
        $tarea = Task::find($id); 

        if (!$tarea) {
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }

        return response()->json($tarea);
    }

    // U - UPDATE
    public function update(Request $request, $id) 
    {
        $tarea = Task::find($id);

        if (!$tarea) {
            return response()->json(['message' => 'Tarea no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'titulo' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|nullable|string',
            'estado' => 'sometimes|required|in:pendiente,en progreso,completada',
            'fecha_vencimiento' => 'sometimes|nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $tarea->update($request->all());
        return response()->json($tarea);
    }

    // D - DELETE
    public function destroy($id) 
    {
        $tarea = Task::find($id);
        
        if (!$tarea) {
            return response()->json(null, 404);
        }
        
        $tarea->delete();
        return response()->json(null, 204);
    }
    
    // Funciones Adicionales
    public function resumen()
    {
        return response()->json([
            'pendientes' => Task::where('estado', 'pendiente')->count(),
            'en_progreso' => Task::where('estado', 'en progreso')->count(),
            'completadas' => Task::where('estado', 'completada')->count(),
        ]);
    }
}