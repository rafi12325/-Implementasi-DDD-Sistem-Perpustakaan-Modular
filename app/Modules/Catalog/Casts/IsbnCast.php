<?php

namespace Modules\Catalog\Casts;

use Modules\Shared\ValueObjects\Isbn;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Custom cast agar atribut isbn pada Book otomatis menjadi
 * Value Object Isbn (validasi format saat set dan get).
 */
class IsbnCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?Isbn
    {
        return $value !== null ? new Isbn($value) : null;
    }

    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) new Isbn($value instanceof Isbn ? (string) $value : $value);
    }
}
