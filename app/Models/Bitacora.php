<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    public $timestamps = false;
    protected $table = 'bitacora'; // Especifica el nombre de la tabla

    protected $fillable = [
        'ip',
        'accion',
        'fecha_hora',
        'id_usuario',
    ];

    // Relaciones con otras tablas (si es necesario)
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'registro_personal', 'registro');
    }
}
