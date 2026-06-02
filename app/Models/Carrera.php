<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    public $timestamps = false;
    protected $table = 'carrera';
    protected $primaryKey = 'codigo';
    public $incrementing = false; // Indica que la PK no es un entero autoincremental
    protected $keyType = 'string';
    
    protected $fillable = [
        'codigo',
        'plan',
        'nombre',
        'modalidad',
        'nivel',
        'tipo',
        'duracion',
    ];

    public function gestiones()
    {
        return $this->belongsToMany(Gestion::class, 'carrera_gestion', 'codigo_carrera', 'codigo_gestion')
                    ->withPivot('cupos', 'id');
    }

    public function carreraGestiones()
    {
        return $this->hasMany(CarreraGestion::class, 'codigo_carrera', 'codigo');
    }

    public function postulantes1()
    {
        return $this->hasMany(Postulante::class, 'codigo_carrera1', 'codigo');
    }

    public function postulantes2()
    {
        return $this->hasMany(Postulante::class, 'codigo_carrera2', 'codigo');
    }
}
