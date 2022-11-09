<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorType extends Model
{
    //
    protected $table = 'visitor_types';

    protected $fillable = [
        'visitorTypeName'
    ];

    public function visitorRecord()
    {
        return $this->BelongsTo(VisitorRecord::class);
    }
}
