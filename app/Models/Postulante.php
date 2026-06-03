<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
    public $timestamps = false;
    protected $table = 'postulante';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo', 'ci', 'procedencia', 'telefono_2', 'plazo',
        'estado', 'gestion_egreso', 'id_requisitos_postulante',
        'id_colegio', 'id_pago', 'id_grupo',
    ];

    public function carreras()
    {
        return $this->belongsToMany(Carrera::class, 'postulante_carrera',
            'codigo_postulante',
            ['codigo_carrera', 'plan_carrera', 'modalidad_carrera']
        )->withPivot('opcion');
    }

    public function requisitosPostulante()
    {
        return $this->belongsTo(RequisitosPostulante::class, 'id_requisitos_postulante');
    }
    public function colegio()
    {
        return $this->belongsTo(Colegio::class, 'id_colegio');
    }
    public function pago()
    {
        return $this->belongsTo(Pago::class, 'id_pago');
    }
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }
    public function datosPersonales()
    {
        return $this->belongsTo(DatosPersonales::class, 'ci', 'ci');
    }
}
