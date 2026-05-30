<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    protected $table = 'examen'; // Especifica el nombre de la tabla
    protected $fillable = [
        'codigo_postulante',
        'nro_examen',
        'ponderacion',
        'nota',
        'fecha',
        'id_materia',
    ];

    // Relaciones con otras tablas (si es necesario)
    public function postulante()
    {
        return $this->belongsTo(Postulante::class, 'codigo_postulante', 'codigo');
    }
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia');
    }
}
