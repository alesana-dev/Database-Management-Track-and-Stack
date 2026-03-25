<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'first_name',
        'last_name',
        'alias',
        'batch_name',
        't_birth',
        'gt_initiated',
        'mww_initiated',
        'status',
        'notes',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(CaseHistory::class)->latest(); // newest first by default
    }

    // Status helpers (optional but clean)
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isExpelled(): bool
    {
        return $this->status === 'expelled';
    }
}
