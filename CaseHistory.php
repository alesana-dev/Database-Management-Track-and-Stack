<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseHistory extends Model
{
    protected $fillable = [
        'member_id',
        'user_id',
        'action',
        'severity',
        'description',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function filedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
