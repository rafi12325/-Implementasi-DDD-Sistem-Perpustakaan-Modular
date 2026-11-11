<?php

namespace Modules\Shared\Exceptions;

use Exception;

/**
 * Dilempar saat stok buku tidak mencukupi sehingga transaction
 * peminjaman harus di-rollback.
 */
class InsufficientStockException extends Exception
{
    public function __construct(
        protected string $bookTitle,
        protected int $available,
        protected int $requested
    ) {
        parent::__construct(
            "Stok buku '{$bookTitle}' tidak mencukupi. " .
            "Tersedia: {$available}, diminta: {$requested}."
        );
    }

    public function getBookTitle(): string
    {
        return $this->bookTitle;
    }

    public function getAvailable(): int
    {
        return $this->available;
    }

    public function getRequested(): int
    {
        return $this->requested;
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'error' => 'insufficient_stock',
            ], 422);
        }

        return back()->withErrors(['stock' => $this->getMessage()]);
    }
}
