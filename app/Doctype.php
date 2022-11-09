<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Doctype extends Model
{
    //
    protected $table = 'doctypes';

    protected $fillable = [
        'name',
        'description',
        'state'
    ];

    public function user()
    {
        return $this->hasOne('App\User');
    }

}
