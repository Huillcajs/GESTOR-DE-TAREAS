<?php

namespace App\Models;

// 1. 🎯 CRÍTICO: Importar el modelo de MongoDB
use MongoDB\Laravel\Eloquent\Model;
// 2. Importar los Traits (son clases, no interfaces)
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
// Nota: También podrías usar el Trait "Notifiable" si lo necesitas

class User extends Model implements 
    AuthenticatableContract, 
    AuthorizableContract 
{
    // 3. 🎯 CRÍTICO: Usar los Traits para proporcionar la funcionalidad de autenticación
    use Authenticatable, Authorizable; 

    // 4. Configuración de MongoDB
    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',         
        'google_id',        
        'github_id',        
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}