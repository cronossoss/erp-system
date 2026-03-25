<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkEntry extends Model
{
    protected $fillable = [
        'employee_id',
        'work_entry_type_id',
        'date',
        'time_from',
        'time_to',
        'note'
    ];

    public function type()
    {
        return $this->belongsTo(\App\Models\WorkEntryType::class, 'work_entry_type_id');
    }
}
