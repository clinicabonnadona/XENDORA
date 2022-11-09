<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitorRecordTime extends Model
{
    //
    protected $table = 'visitor_record_times';

    protected $fillable = [
        'visitorEntryDate',
        'visitorOutDate',
        'visitorHoursDuration',
        'family_income_record_id',
        'patientAdmConsecutive',
        'visitor_record_id',
    ];

    public function visitorRecord()
    {
        return $this->hasMany(VisitorRecord::class);
    }

    public function FamilyIncomeRecord()
    {
        return $this->hasOne(FamilyIncomeRecord::class);
    }
}
