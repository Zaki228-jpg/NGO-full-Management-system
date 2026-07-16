<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'slug',
        'content',
        'cover_image',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (News $news) {
            $news->slug = Str::slug($news->title) . '-' . Str::random(5);
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
