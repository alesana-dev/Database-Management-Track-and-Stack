<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'chapter_id',
        'member_id',
        'action',
        'entity',
        'model',
        'before',
        'after',
        'note',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    // Optional: member relation may be null after permanent delete
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
