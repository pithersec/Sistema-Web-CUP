<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora'; // Especifica el nombre de la tabla

    protected $fillable = [
        'accion',
        'fecha',
        'registro_personal',
    ];

    // Relaciones con otras tablas (si es necesario)
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'registro_personal', 'registro');
    }
}
