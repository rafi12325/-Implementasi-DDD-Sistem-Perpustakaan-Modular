<?php

namespace Modules\Circulation\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Circulation\Events\BookBorrowed;

/**
 * Listener untuk mencatat log peminjaman buku.
 */
class LogBookBorrowed
{
    public function handle(BookBorrowed $event): void
    {
        $loan = $event->loan;

        Log::info('[Circulation] Buku dipinjam', [
            'loan_number' => $loan->loan_number,
            'member_number' => $loan->member?->member_number,
            'books' => $loan->items->pluck('book_title')->all(),
            'due_at' => $loan->due_at?->toDateTimeString(),
        ]);
    }
}
