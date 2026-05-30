<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitosPostulante extends Model
{
    protected $table = 'requisitos_postulante';

    protected $fillable = [
        'titulo_original',
        'titulo_copia',
        'fotocopia_carnet',
        'formulario',
        'comprobante',
        'libreta',
    ];
}
