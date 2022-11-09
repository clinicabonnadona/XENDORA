<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FamilyIncomeRecord extends Model
{
    //
    protected $table = 'family_income_records';

    protected $fillable = [
        'patientName',
        'patientDocument',
        'patientDocumentType',
        'patientAdmConsecutive',
        'patientAdmDate',
        'patientHabitation',
        'patientOutDate',
        'user_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function visitorRecord()
    {
        return $this->belongsTo(VisitorRecord::class);
    }

    public function visitRecordTime()
    {
        return $this->belongsTo(VisitorRecordTime::class);
    }
}
