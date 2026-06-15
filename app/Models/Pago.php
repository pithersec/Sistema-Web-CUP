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
        'id_transaccion',
        'moneda',
    ];

    protected $casts = ['fecha' => 'datetime', 'monto' => 'decimal:2'];

    public function postulantes()
    {
        return $this->hasMany(Postulante::class, 'id_pago');
    }
}