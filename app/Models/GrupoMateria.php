<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoMateria extends Model
{
    public $timestamps = false;
    protected $table = 'grupo_materia';

    protected $fillable = [
        'id_materia',
        'id_grupo',
        'gestion_grupo',
        'horario',
        'registro_personal',
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id')
            ->where('codigo_gestion', $this->gestion_grupo);
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'registro_personal', 'registro');
    }
}
