<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'full_name',
        'position',
        'phone',
        'hire_date',
        'salary',
        'status', // active, on_leave, terminated
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'salary' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'employee_project')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
