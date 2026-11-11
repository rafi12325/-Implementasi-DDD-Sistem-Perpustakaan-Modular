<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory;

    protected static function newFactory(): \Database\Factories\AuthorFactory
    {
        return \Database\Factories\AuthorFactory::new();
    }

    protected $fillable = ['name', 'slug', 'biography'];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
