<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_number',
        'first_name',
        'last_name',
        'position',
        'organizational_unit_id',
        'contract_type',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function organizationalUnit()
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
