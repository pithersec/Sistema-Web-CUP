<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    public $timestamps = false;
    protected $table = 'bitacora'; 

    protected $fillable = [
        'ip',
        'accion',
        'fecha_hora',
        'id_usuario',
    ];

    // Relación con el usuario que ejecutó la acción
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'registro_personal', 'registro');
    }
}