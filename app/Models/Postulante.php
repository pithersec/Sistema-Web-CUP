<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
    public $timestamps = false;
    protected $table = 'postulante'; // Especifica el nombre de la tabla
    protected $primaryKey = 'codigo'; // Especifica la clave primaria
    public $incrementing = false; // Indica que la clave primaria no es auto-incremental
    protected $keyType = 'string'; // Especifica el tipo de la clave primaria

    protected $fillable = [
        'codigo',
        'ci',
        'procedencia',
        'telefono_2',
        'plazo',
        'estado',
        'gestion_egreso',
        'id_requisitos_postulante',
        'id_colegio',
        'id_pago',
        'id_grupo',
        'codigo_carrera1',
        'codigo_carrera2',
    ];

    // Relaciones con otras tablas (si es necesario)
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
    public function carrera1()
    {
        return $this->belongsTo(Carrera::class, 'codigo_carrera1', 'codigo');
    }
    public function carrera2()
    {
        return $this->belongsTo(Carrera::class, 'codigo_carrera2', 'codigo');
    }
    public function datosPersonales()
    {
        return $this->belongsTo(DatosPersonales::class, 'ci', 'ci');
    }
}
