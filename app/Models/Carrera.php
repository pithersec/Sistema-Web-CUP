<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    public $timestamps = false;
    protected $table = 'carrera';
    public $incrementing = false;
    protected $primaryKey = null; // PK compuesta, Eloquent no la maneja nativamente

    protected $fillable = [
        'codigo', 'plan', 'nombre', 'modalidad', 'nivel', 'tipo', 'duracion',
    ];

    public function gestiones()
    {
        return $this->belongsToMany(Gestion::class, 'carrera_gestion', 
            ['codigo_carrera', 'plan_carrera', 'modalidad_carrera'],
            'codigo_gestion'
        )->withPivot('cupos');
    }

    public function postulantes()
    {
        return $this->belongsToMany(Postulante::class, 'postulante_carrera',
            ['codigo_carrera', 'plan_carrera', 'modalidad_carrera'],
            'codigo_postulante'
        )->withPivot('opcion');
    }
}
