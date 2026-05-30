<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colegio extends Model
{
    protected $table = 'colegio'; // Especifica el nombre de la tabla
    public $timestamps = false; // Si no tienes columnas created_at y updated_at

    protected $fillable = [
        'cie',
        'nombre',
        'tipo',
        'turno',
        'pais',
        'departamento',
        'provincia',
    ];
}
