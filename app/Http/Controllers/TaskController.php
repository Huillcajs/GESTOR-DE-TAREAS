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

        $estado = $request->input('estado'); // Captura el valor del select
        
        // 🎯 CORRECCIÓN CLAVE: Aplicar filtro SOLO si el valor de 'estado' no está vacío
        if ($estado && $estado !== '') { 
            $query->where('estado', $estado);
        }
        
        // Ejecuta la consulta
        $tasks = $query->get(); 

        // Pasa las tareas y el estado actual (para mantener la selección del select) a la vista
        return view('tasks.index', [
            'tasks' => $tasks,
            'selected_estado' => $estado // Opcional, pero ayuda a mantener la selección
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