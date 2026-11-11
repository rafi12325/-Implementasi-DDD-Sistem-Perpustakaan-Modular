<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Kategori dengan self-referencing (Kategori -> Sub-kategori)
 * dan Many-to-Many dengan Buku melalui pivot book_category.
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): \Database\Factories\CategoryFactory
    {
        return \Database\Factories\CategoryFactory::new();
    }

    protected $fillable = ['name', 'slug', 'description', 'parent_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_category')
            ->withPivot('added_at');
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
