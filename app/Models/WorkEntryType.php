<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkEntryType extends Model
{
    protected $fillable = [
    'employee_id',
    'work_entry_type_id',
    'date',
    'time_from',
    'time_to',
    'note'
];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
