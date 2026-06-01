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

    protected $fillable = [
        'registro',
        'ci',
    ];

    public function datosPersonales()
    {
        return $this->belongsTo(DatosPersonales::class, 'ci', 'ci');
    }
}
