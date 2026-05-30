<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pago';
    public $timestamps = false;

    protected $fillable = [
        'monto',
        'fecha',
        'concepto',
        'estado',
        'referencia_pasarela',
    ];
}
