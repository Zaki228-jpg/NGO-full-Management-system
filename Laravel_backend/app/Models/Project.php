<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'sector', // education, health, wash, livelihood, etc.
        'province',
        'budget',
        'status', // planned, ongoing, completed, suspended
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Project $project) {
            $project->slug = Str::slug($project->title) . '-' . Str::random(5);
        });
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_project')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function scopeOngoing(Builder $query): Builder
    {
        return $query->where('status', 'ongoing');
    }

    public function totalRaised(): float
    {
        return (float) $this->donations()->completed()->sum('amount');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
