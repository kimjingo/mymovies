<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'user_id',
        'user_media_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userMedia()
    {
        return $this->belongsTo(UserMedia::class);
    }
}
