<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'head_employee_id',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function head(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Employee::class, 'head_employee_id');
    }
}
