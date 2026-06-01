<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
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
        return $this->belongsToMany(Gestion::class, 'carrera_gestion', 'codigo_carrera', 'id_gestion')
                    ->withPivot('cupos')
                    ->withTimestamps();
    }



}
