<?php

namespace Modules\Catalog\Services;

use Modules\Catalog\Models\Book;
use Modules\Shared\Contracts\BookServiceInterface;

/**
 * Implementasi contract BookServiceInterface.
 * Satu-satunya pintu masuk Circulation Context ke data buku.
 */
class CatalogService implements BookServiceInterface
{
    public function getBookSnapshot(int $bookId): ?array
    {
        $book = Book::find($bookId);

        if (! $book) {
            return null;
        }

        return [
            'id' => $book->id,
            'title' => $book->title,
            'isbn' => (string) $book->isbn,
            'stock' => $book->stock,
        ];
    }

    public function isBookAvailable(int $bookId, int $quantity): bool
    {
        $book = Book::find($bookId);

        return $book && $book->is_active && $book->isInStock($quantity);
    }

    public function reduceStock(int $bookId, int $quantity): bool
    {
        $book = Book::where('is_active', true)->lockForUpdate()->find($bookId);

        if (! $book || ! $book->isInStock($quantity)) {
            return false;
        }

        $book->decrement('stock', $quantity);

        return true;
    }
}
