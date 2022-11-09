<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitingHour extends Model
{
    //
    protected $table = 'visiting_hours';

    protected $fillable = [
        'visitingHoursName',
        'initialHour',
        'finalHour',
        'user_id',
    ];
}
