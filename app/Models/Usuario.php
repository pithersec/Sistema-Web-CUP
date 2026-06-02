<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_name',
        'clave',
        'email',
        'id_perfil',
        'registro_personal',
    ];

    protected $hidden = [
        'clave',
    ];

    protected function casts(): array
    {
        return [
            'clave' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->clave;
    }

    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'id_perfil', 'id');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'registro_personal', 'registro');
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'id_usuario');
    }

    public function tienePrivilegio(string $privilegioNombre): bool
    {
        return $this->perfil?->privilegios?->pluck('nombre')->contains($privilegioNombre) ?? false;
    }
}