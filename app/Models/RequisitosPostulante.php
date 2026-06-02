<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitosPostulante extends Model
{
    protected $table = 'requisitos_postulante';
public $timestamps = false;
    protected $fillable = [
        'titulo_original',
        'titulo_copia',
        'fotocopia_carnet',
        'formulario',
        'comprobante',
        'libreta',
    ];

    protected $casts = [
        'titulo_original' => 'boolean',
        'titulo_copia' => 'boolean',
        'fotocopia_carnet' => 'boolean',
        'formulario' => 'boolean',
        'comprobante' => 'boolean',
        'libreta' => 'boolean',
    ];

    public function postulantes()
    {
        return $this->hasMany(Postulante::class, 'id_requisitos_postulante');
    }
}
