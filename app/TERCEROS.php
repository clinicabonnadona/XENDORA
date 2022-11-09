<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TERCEROS extends Model
{
    //
    protected $table = 'TERCEROS';

    protected $fillable = [
        'TerCod',
        'tipdoc_id',
        'TerRazSoc',
        'tipterc_id',
        'TerDir',
        'TerTel',
        'TerActNeg',
        'TerEst',
        'user_id',
    ];


    public function tipdoc()
    {
        return $this->belongsTo(TIPDOC::class);
    }

    public function tipter()
    {
        return $this->belongsTo(TIPTERC::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function maenego()
    {
        return $this->hasOne(MAENEGO::class);
    }

    public function acucorlab()
    {
        return $this->hasOne(ACUCORLAB::class);
    }
}
