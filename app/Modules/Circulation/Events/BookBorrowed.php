<?php

namespace Modules\Circulation\Events;

use Modules\Circulation\Models\Loan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Domain Event: ditembakkan setelah peminjaman berhasil.
 */
class BookBorrowed
{
    use Dispatchable, SerializesModels;

    public function __construct(public Loan $loan) {}
}
