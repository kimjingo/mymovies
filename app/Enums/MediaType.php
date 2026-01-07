<?php

namespace App\Enums;

enum MediaType: int
{
    case Movie = 1;
    case TvShow = 2;

    public function label(): string
    {
        return match($this) {
            self::Movie => 'Movie',
            self::TvShow => 'TV Show',
        };
    }

    public static function options(): array
    {
        return [
            self::Movie->value => self::Movie->label(),
            self::TvShow->value => self::TvShow->label(),
        ];
    }

    public static function fromString(string $value): self
    {
        return match(strtolower($value)) {
            'tv_show', 'tv show', 'tvshow', '2' => self::TvShow,
            default => self::Movie,
        };
    }
}
