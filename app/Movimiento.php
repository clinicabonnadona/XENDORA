<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    //
    protected $table = 'movimientos';
    protected $fillable = [
        'MSRESO',
        'saldo',
        'rotacion',
        'mes',
        'nomMes',
        'Anio',
        'fechAct'
    ];
}
