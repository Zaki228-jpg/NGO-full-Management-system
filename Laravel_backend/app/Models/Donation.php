<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'project_id',
        'amount',
        'currency',
        'method', // bank_transfer, card, cash, mobile_money
        'status', // pending, completed, failed, refunded
        'reference',
        'donated_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'donated_at' => 'datetime',
        ];
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
