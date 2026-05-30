<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoMateria extends Model
{
    protected $table = 'grupo_materia';
    public $incrementing = false; // No hay ID autoincremental
    protected $keyType = 'string'; // Claves foráneas como strings

    protected $fillable = [
        'id_materia',
        'id_grupo',
        'horario',
        'registro_personal',
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'registro_personal', 'registro');
    }
}
