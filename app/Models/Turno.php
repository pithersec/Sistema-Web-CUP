<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'turno';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'nombre';
    protected $keyType = 'string';

    protected $fillable = ['nombre', 'hora_inicio', 'hora_fin'];

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'nombre_turno', 'nombre');
    }

    public function postulantes()
    {
        return $this->hasMany(Postulante::class, 'nombre_turno', 'nombre');
    }
}