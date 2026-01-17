<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\MediaType;

class MediaPool extends Model
{
    protected $table = 'media_pool';

    protected $fillable = [
        'title',
        'type',
        'description',
        'release_year',
        'poster_url',
        'created_by',
    ];

    protected $casts = [
        'type' => MediaType::class,
    ];

    public function userMedia()
    {
        return $this->hasMany(UserMedia::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('title', 'LIKE', "%{$term}%");
    }

    public function scopeMovies($query)
    {
        return $query->where('type', MediaType::Movie->value);
    }

    public function scopeTvShows($query)
    {
        return $query->where('type', MediaType::TvShow->value);
    }

    public function getTypeNameAttribute(): string
    {
        return $this->type->label();
    }

    /**
     * Check if the media pool can be edited by the given user.
     * Can be edited only if:
     * - Created by is NULL (anyone can edit and claim it)
     * - OR Created by the user
     * - Not used by any other user
     * - Has no likes
     */
    public function canBeEditedBy($userId): bool
    {
        // If created_by is NULL, anyone can edit it (they'll become the creator)
        if ($this->created_by === null) {
            // Still check if it's used by other users or has likes
            $userMediaCount = $this->userMedia()->count();
            if ($userMediaCount > 1) {
                return false;
            }

            if ($userMediaCount === 1 && $this->userMedia()->where('user_id', '!=', $userId)->exists()) {
                return false;
            }

            $hasLikes = $this->userMedia()->whereHas('likes')->exists();
            if ($hasLikes) {
                return false;
            }

            return true;
        }

        // Check if created by the user
        if ($this->created_by !== $userId) {
            return false;
        }

        // Check if used by other users (more than one user_media entry or the single entry is not by this user)
        $userMediaCount = $this->userMedia()->count();
        if ($userMediaCount > 1) {
            return false;
        }

        if ($userMediaCount === 1 && $this->userMedia()->where('user_id', '!=', $userId)->exists()) {
            return false;
        }

        // Check if has any likes
        $hasLikes = $this->userMedia()->whereHas('likes')->exists();
        if ($hasLikes) {
            return false;
        }

        return true;
    }
}
