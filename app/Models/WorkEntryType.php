<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkEntryType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'is_paid',
        'counts_as_work',
        'affects_vacation',
    ];
}