<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ACUCORLAB extends Model
{
    //
    protected $table = 'ACUCORLAB';

    protected $fillable = [
        'tercero_id',
        'ACLPerNc',
        'ACLProGNc',
        'ACLAdiDPp',
    ];

    public function tercero()
    {
        return $this->belongsTo(TERCEROS::class);
    }
}
