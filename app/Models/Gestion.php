<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    public $timestamps = false;
    protected $table = 'gestion';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = ['fecha_ini' => 'date', 'fecha_fin' => 'date'];

    protected $fillable = [
        'codigo',
        'fecha_ini',
        'fecha_fin',
    ];

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'codigo_gestion', 'codigo');
    }

    public function carreraGestiones()
    {
        return $this->hasMany(CarreraGestion::class, 'codigo_gestion', 'codigo');
    }

    public function carreras()
    {
        return $this->belongsToMany(Carrera::class, 'carrera_gestion', 'codigo_gestion', 'codigo_carrera')
                    ->withPivot('cupos', 'id');
    }
}
