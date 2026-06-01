<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;
public $timestamps = false;
    protected $table = 'usuario'; // Tu tabla personalizada

    // 1. SI TU LLAVE PRIMARIA EN LA BASE DE DATOS NO SE LLAMA 'id', CAMBIA ESTO:
    // protected $primaryKey = 'id_usuario'; 
    // Si se llama 'id', déjala así:
    protected $primaryKey = 'id'; 

    // 2. Si tu ID es un texto/string (ej: 'USR-01'), descomenta la línea de abajo:
    // public $incrementing = false;
    // protected $keyType = 'string';

    protected $fillable = [
        'user_name',
        'clave',
        'email',
        'id_perfil',
        'registro_personal',
    ];
    
    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'id_perfil', 'id');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'registro_personal', 'registro');
    }
    
    protected $hidden = [
        'clave',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'clave' => 'hashed',
        ];
    }

    /**
     * ATENCIÓN: Este método es OBLIGATORIO para decirle a Laravel 
     * que use la columna 'clave' en lugar de 'password'
     */
    public function getAuthPassword()
    {
        return $this->clave;
    }
}