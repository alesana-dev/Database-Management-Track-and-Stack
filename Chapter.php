<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'history',
        'founders',
        'vicinity_area',
        'status',
    ];

    /**
     * A chapter has many members
     */
    public function members()
    {
        return $this->hasMany(\App\Models\Member::class);
    }

    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

}
