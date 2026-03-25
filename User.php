<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    public const ROLE_SUPER_ADMIN   = 'super_admin';
    public const ROLE_CHAPTER_ADMIN = 'chapter_admin';

    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role',
        'chapter_id',
        'first_login',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'first_login' => 'boolean',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isChapterAdmin(): bool
    {
        return $this->role === self::ROLE_CHAPTER_ADMIN;
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
