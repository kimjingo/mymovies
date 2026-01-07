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
    ];

    protected $casts = [
        'type' => MediaType::class,
    ];

    public function userMedia()
    {
        return $this->hasMany(UserMedia::class);
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
}
