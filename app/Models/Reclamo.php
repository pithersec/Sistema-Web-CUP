<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reclamo extends Model
{
    protected $table = 'reclamo'; // Especifica el nombre de la tabla
public $timestamps = false;
    protected $casts = ['fecha' => 'datetime'];
    protected $fillable = [
        'descripcion',
        'fecha',
        'dirigido',
        'estado',
        'codigo_postulante',
        'registro_personal',
    ];

    // Relaciones con otras tablas (si es necesario)
    public function postulante()
    {
        return $this->belongsTo(Postulante::class, 'codigo_postulante', 'codigo');
    }
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'registro_personal', 'registro');
    }
}
