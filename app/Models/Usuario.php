<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuario'; // Especificar el nombre de la tabla personalizada

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'clave',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'clave' => 'hashed',
        ];
    }

    /**
     * Reemplazar el método nativo de Laravel para que valide con 'clave' en lugar de 'password'
     */
    public function getAuthPassword()
    {
        return $this->clave;
    }



}
