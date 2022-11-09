<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitorRecord extends Model
{
    //
    protected $table = 'visitor_records';

    protected $fillable = [
        'visitorDocument',
        'visitorDocumentType',
        'visitorName',
        'visitorRelationship',
        'family_income_record_id',
        'visitor_type_id',
    ];

    public function familyIncomeRecords()
    {
        return $this->hasMany(FamilyIncomeRecord::class);
    }

    public function visitorType()
    {
        return $this->hasOne(VisitorType::class);
    }

    public function visitorRecordTime()
    {
        return $this->belongsTo(VisitorRecordTime::class);
    }
}
