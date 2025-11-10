<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📋 Gestor de Tareas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    
    {{-- ENCABEZADO Y LOGOUT --}}
    @auth
        {{-- Muestra el nombre del usuario logueado --}}
        <h1 class="mb-4 d-inline-block">Bienvenido, {{ auth()->user()->name }}</h1>
        
        {{-- Botón de Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="d-inline-block float-end">
            @csrf
            <button type="submit" class="btn btn-outline-secondary mt-2">Cerrar Sesión</button>
        </form>
    @else
        <h1 class="mb-4 d-inline-block">📋 Gestor de Tareas</h1>
    @endauth

    <hr>

    {{-- Botón para Crear Tarea (Abre Modal) --}}
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#taskModal">
        + Crear Nueva Tarea
    </button>

    {{-- Filtros (Usando un formulario GET de Blade) --}}
    <form method="GET" action="{{ route('tasks.index') }}" class="row mb-4">
        <div class="col-md-4">
            <label for="filter-state" class="form-label">Filtrar por Estado:</label>
            <select id="filter-state" name="estado" class="form-select" onchange="this.form.submit()">
                {{-- La opción "Todas" debe tener value="" --}}
                <option value="" @if(request('estado') == '') selected @endif>Todas</option>
                <option value="pendiente" @if(request('estado') == 'pendiente') selected @endif>Pendiente</option>
                <option value="en progreso" @if(request('estado') == 'en progreso') selected @endif>En Progreso</option>
                <option value="completada" @if(request('estado') == 'completada') selected @endif>Completada</option>
            </select>
        </div>
    </form>

    {{-- Lista de Tareas (Bucle de Blade) --}}
    <div class="card">
        <div class="card-header">Lista de Tareas</div>
        <ul class="list-group list-group-flush" id="task-list">
            @if(isset($tasks) && $tasks->count() > 0)
                @foreach($tasks as $task)
                    @php
                        // Determinación de clases
                        $badgeClass = '';
                        if ($task->estado === 'completada') $badgeClass = 'bg-success';
                        elseif ($task->estado === 'en progreso') $badgeClass = 'bg-warning text-dark';
                        else $badgeClass = 'bg-secondary';

                        // Formatear la fecha de vencimiento
                        $dueDate = $task->fecha_vencimiento ? \Carbon\Carbon::parse($task->fecha_vencimiento)->format('d/m/Y') : 'N/A';
                        
                        // Formatear la fecha de creación
                        $createdAt = \Carbon\Carbon::parse($task->created_at)->format('d/m/Y');
                        
                        // Usamos $task->_id.
                        $taskId = $task->_id; 
                    @endphp
                    
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5>{{ $task->titulo }}
                                <span class="badge {{ $badgeClass }} ms-2">{{ strtoupper($task->estado) }}</span>
                            </h5>
                            <p class="mb-1">{{ $task->descripcion }}</p>
                            
                            {{-- Mostrar Fecha de Creación y Vencimiento --}}
                            <small class="text-muted d-block">Creado: {{ $createdAt }}</small>
                            <small class="text-muted">Vence: {{ $dueDate }}</small>
                        </div>
                        <div>
                            {{-- ENLACE DE EDICIÓN: Redirige a la página individual --}}
                            <a href="{{ route('tasks.edit', ['id' => $taskId]) }}" 
                                class="btn btn-sm btn-info text-white me-2">
                                ✏️
                            </a>
                            
                            {{-- Botón para ELIMINAR (usa JavaScript/Axios) --}}
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteTask('{{ $taskId }}')">
                                🗑️
                            </button>
                        </div>
                    </li>
                @endforeach
            @else
                <li class="list-group-item text-center text-info">No hay tareas para mostrar.</li>
            @endif
        </ul>
    </div>
</div>

{{-- MODAL PARA CREAR TAREA (Requiere JS para la llamada API) --}}
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">Crear Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="taskForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="en progreso">En Progreso</option>
                            <option value="completada">Completada</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                        <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="save-task-btn">Guardar Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Scripts mínimos necesarios --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    const API_URL = '/api/tareas'; 

    // Lógica para CREAR Tarea (POST)
    document.getElementById('taskForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            await axios.post(API_URL, data);

            alert('Tarea creada con éxito. Recargando la lista...');
            
            // Cerrar modal y RECARGAR LA PÁGINA para que Blade muestre el nuevo elemento
            const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
            modal.hide();
            window.location.reload(); 

        } catch (error) {
            console.error('Error al crear la tarea:', error.response);
            alert('Error al crear la tarea. Verifique los datos.');
        }
    });

    // Lógica para ELIMINAR Tarea (DELETE)
    async function deleteTask(id) {
        if (!confirm('¿Estás seguro de que quieres eliminar esta tarea?')) {
            return;
        }

        try {
            await axios.delete(`${API_URL}/${id}`);
            alert('Tarea eliminada con éxito. Recargando la lista...');
            window.location.reload(); // Recargar la página para que Blade actualice la lista
        } catch (error) {
            console.error('Error al eliminar la tarea:', error);
            alert('Error al eliminar la tarea.');
        }
    }
</script>

</body>
</html>