<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Asegura que se use la conexión y colección de MongoDB
    protected $connection = 'mongodb'; 
    protected $collection = 'tareas'; 

    protected $fillable = [
        'titulo',
        'descripcion',
        'estado', 
        'fecha_vencimiento',
        'usuario_asignado',
    ];

    protected $casts = [
        '_id' => 'string',
        'fecha_vencimiento' => 'datetime',
    ];
}