<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMedia extends Model
{
    protected $table = 'user_media';

    protected $fillable = [
        'user_id',
        'media_pool_id',
        'visibility',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mediaPool()
    {
        return $this->belongsTo(MediaPool::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function likedBy()
    {
        return $this->belongsToMany(User::class, 'likes')
            ->withTimestamps();
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('visibility', 'private');
    }

    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function isPrivate(): bool
    {
        return $this->visibility === 'private';
    }
}
