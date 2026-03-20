<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'hire_date',
        'position',
        'organizational_unit_id',
        'active'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
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
