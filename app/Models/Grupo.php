<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupo';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = null;

    protected $fillable = [
        'id', 'aula', 'nombre_turno', 'total_ins', 'codigo_gestion',
    ];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'codigo_gestion', 'codigo');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'nombre_turno', 'nombre');
    }

    public function postulantes()
    {
        return $this->hasMany(Postulante::class, 'id_grupo');
    }

    public function grupoMaterias()
    {
        return $this->hasMany(GrupoMateria::class, 'id_grupo');
    }
}