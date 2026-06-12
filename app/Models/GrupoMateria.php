<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoMateria extends Model
{
    public $timestamps = false;
    protected $table = 'grupo_materia';
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'id_materia',
        'id_grupo',
        'gestion_grupo',
        'hora_inicio',
        'hora_fin',
        'orden',
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

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'id_materia', 'id_materia');
    }
}