<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary mb-4">← Volver a la Lista</a>
    
    <h1 class="mb-4" id="page-title">Cargando Tarea...</h1>

    <div class="card p-4">
        <form id="taskForm">
            {{-- El ID se inyecta desde la ruta web --}}
            <input type="hidden" id="task_id" name="id" value="{{ $taskId }}">

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
            
            <button type="submit" class="btn btn-primary" id="save-task-btn">Guardar Cambios</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    const API_URL = '/api/tareas';
    const taskId = document.getElementById('task_id').value;
    const taskForm = document.getElementById('taskForm');

    async function loadTaskData() {
        if (!taskId || taskId === 'undefined') {
            document.getElementById('page-title').textContent = 'Error: ID de Tarea no válido';
            alert('Error al cargar los datos de la tarea. Asegúrate que el ID es válido.');
            taskForm.querySelectorAll('input, select, textarea, button').forEach(el => el.disabled = true);
            return;
        }

        try {
            const response = await axios.get(`${API_URL}/${taskId}`);
            const task = response.data;
            
            document.getElementById('page-title').textContent = `Editar Tarea: ${task.titulo}`;
            document.getElementById('titulo').value = task.titulo;
            document.getElementById('descripcion').value = task.descripcion;
            document.getElementById('estado').value = task.estado;
            
            if (task.fecha_vencimiento) {
                const datePart = task.fecha_vencimiento.substring(0, 10); 
                document.getElementById('fecha_vencimiento').value = datePart;
            }

        } catch (error) {
            console.error('Error al cargar la tarea:', error.response ? error.response.data : error);
            document.getElementById('page-title').textContent = 'Error: Tarea no encontrada';
            alert('Error al cargar los datos de la tarea. Asegúrate que el ID es válido.');
            taskForm.querySelectorAll('input, select, textarea, button').forEach(el => el.disabled = true);
        }
    }

    // Función para manejar el envío del formulario (UPDATE)
    taskForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            await axios.put(`${API_URL}/${taskId}`, data);

            alert('Tarea actualizada con éxito.');
            
            // Redirigir al usuario de vuelta a la lista principal
            window.location.href = '{{ route('tasks.index') }}'; 

        } catch (error) {
            console.error('Error al actualizar la tarea:', error.response);
            alert('Error al guardar los cambios. Verifique la conexión o los datos.');
        }
    });

    loadTaskData();
</script>

</body>
</html>