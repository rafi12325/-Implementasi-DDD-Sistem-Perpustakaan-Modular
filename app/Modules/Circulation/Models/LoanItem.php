<?php

namespace Modules\Circulation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DetailPeminjaman: entity dalam aggregate Loan.
 * Menyimpan snapshot judul & ISBN buku pada saat peminjaman.
 */
class LoanItem extends Model
{
    protected $fillable = [
        'loan_id', 'book_id', 'book_title', 'book_isbn', 'quantity', 'returned_at',
    ];

    protected $casts = [
        'returned_at' => 'datetime',
        'quantity' => 'integer',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
