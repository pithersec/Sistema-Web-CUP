<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    public $timestamps = false;
    protected $table = 'personal';
    protected $primaryKey = 'registro';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = ['estado' => 'boolean'];

    protected $fillable = [
        'registro',
        'ci',
        'estado',
    ];

    public function datosPersonales()
    {
        return $this->belongsTo(DatosPersonales::class, 'ci', 'ci');
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'registro_personal', 'registro');
    }

    public function grupoMaterias()
    {
        return $this->hasMany(GrupoMateria::class, 'registro_personal', 'registro');
    }

    public function requisitosPersonal()
    {
        return $this->hasMany(RequisitosPersonal::class, 'registro_personal', 'registro');
    }

    public function reclamos()
    {
        return $this->hasMany(Reclamo::class, 'registro_personal', 'registro');
    }
}
