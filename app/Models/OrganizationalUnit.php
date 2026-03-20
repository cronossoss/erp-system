<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationalUnit extends Model
{
    protected $fillable = [
        'name',
        'parent_id'
    ];

    public function parent()
    {
        return $this->belongsTo(OrganizationalUnit::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(OrganizationalUnit::class, 'parent_id')
            ->orderBy('name');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'organizational_unit_id');
    }
}
