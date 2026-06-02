<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarreraGestion extends Model
{
    protected $table = 'carrera_gestion';
    public $timestamps = false;

    protected $fillable = [
        'codigo_carrera',
        'codigo_gestion',
        'cupos',
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'codigo_carrera', 'codigo');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'codigo_gestion', 'codigo');
    }
}
