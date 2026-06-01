<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
public $timestamps = false;
    protected $table = 'gestion';
    protected $primaryKey = 'codigo';
    public $incrementing = false; // Indica que la PK no es un entero autoincremental
    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'fecha_ini',
        'fecha_fin',
    ];
}
