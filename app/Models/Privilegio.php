<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Privilegio extends Model
{
    public $timestamps = false;
    protected $table = 'privilegio';
    protected $fillable = ['nombre'];
}
