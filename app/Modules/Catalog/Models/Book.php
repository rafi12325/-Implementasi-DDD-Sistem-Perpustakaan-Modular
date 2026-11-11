<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Catalog\Casts\IsbnCast;

/**
 * Aggregate root dari Catalog Context (Buku).
 * Soft deletes diterapkan; ISBN divalidasi melalui Value Object.
 */
class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): \Database\Factories\BookFactory
    {
        return \Database\Factories\BookFactory::new();
    }

    protected $fillable = [
        'title', 'slug', 'isbn', 'description',
        'author_id', 'publisher_id', 'publication_year',
        'stock', 'is_active',
    ];

    protected $casts = [
        'isbn' => IsbnCast::class,
        'publication_year' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'book_category')
            ->withPivot('added_at');
    }

    /* ---------------- Query Scopes (Query Optimization) ---------------- */

    /** Buku tersedia untuk dipinjam. */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('stock', '>', 0)->where('is_active', true);
    }

    /** Pencarian judul. */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        return $query->when($keyword, fn (Builder $q) => $q->where(
            fn (Builder $inner) => $inner
                ->where('title', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%")
        ));
    }

    /** Filter berdasarkan kategori (termasuk sub-kategori). */
    public function scopeByCategory(Builder $query, ?int $categoryId): Builder
    {
        return $query->when($categoryId, fn (Builder $q) => $q->whereHas(
            'categories',
            fn (Builder $c) => $c->where(function (Builder $cc) use ($categoryId) {
                $cc->where('categories.id', $categoryId)
                    ->orWhere('categories.parent_id', $categoryId);
            })
        ));
    }

    public function isInStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }
}
