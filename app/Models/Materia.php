<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materia'; // Especifica el nombre de la tabla
    public $timestamps = false; // Si no tienes columnas created_at y updated_at

    protected $fillable = [
        'nombre',
        'duracion',
    ];
}
