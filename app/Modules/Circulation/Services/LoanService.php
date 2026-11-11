<?php

namespace Modules\Circulation\Services;

use Modules\Circulation\Events\BookBorrowed;
use Modules\Circulation\Events\BookReturned;
use Modules\Circulation\Models\Fine as FineModel;
use Modules\Circulation\Models\Loan;
use Modules\Circulation\Models\LoanItem;
use Modules\Shared\Contracts\BookServiceInterface;
use Modules\Shared\Contracts\MemberServiceInterface;
use Modules\Shared\Exceptions\InsufficientStockException;
use Modules\Shared\ValueObjects\Fine;
use Illuminate\Support\Facades\DB;

class LoanService
{
    public function __construct(
        private BookServiceInterface $bookService,
        private MemberServiceInterface $memberService
    ) {}

    /**
     * Peminjaman buku dengan DATABASE TRANSACTION.
     * Dalam satu transaksi atomik:
     *  1. kurangi stok buku (via contract ke Catalog Context),
     *  2. buat record peminjaman + detail (snapshot),
     *  3. update status anggota (via contract ke Membership Context).
     * Jika stok tidak mencukupi -> InsufficientStockException -> ROLLBACK.
     */
    public function borrow(int $memberId, array $items, int $loanDays = 7): Loan
    {
        $member = $this->memberService->getMemberForLoan($memberId);

        if (! $member) {
            throw new \Exception("Anggota tidak ditemukan: {$memberId}");
        }

        if ($member['status'] === 'suspended') {
            throw new \Exception("Anggota {$member['member_number']} sedang disuspend, tidak dapat meminjam.");
        }

        $loan = DB::transaction(function () use ($memberId, $items, $loanDays) {
            $loan = Loan::create([
                'loan_number' => 'LN-' . now()->format('Ymd') . '-' . strtoupper(uniqid()),
                'member_id' => $memberId,
                'status' => 'borrowed',
                'borrowed_at' => now(),
                'due_at' => now()->addDays($loanDays),
            ]);

            foreach ($items as $item) {
                $book = $this->bookService->getBookSnapshot($item['book_id']);

                if (! $book) {
                    throw new \Exception("Buku tidak ditemukan: {$item['book_id']}");
                }

                // Invariant: stok harus mencukupi, jika tidak -> rollback
                if (! $this->bookService->isBookAvailable($item['book_id'], $item['quantity'])) {
                    throw new InsufficientStockException(
                        $book['title'],
                        $book['stock'],
                        $item['quantity']
                    );
                }

                // DetailPeminjaman menyimpan snapshot buku
                LoanItem::create([
                    'loan_id' => $loan->id,
                    'book_id' => $book['id'],
                    'book_title' => $book['title'],
                    'book_isbn' => $book['isbn'],
                    'quantity' => $item['quantity'],
                ]);

                $this->bookService->reduceStock($item['book_id'], $item['quantity']);
            }

            // Update status anggota di Membership Context melalui contract
            $this->memberService->markAsBorrowing($memberId);

            return $loan->load('items');
        });

        // Domain event ditembakkan SETELAH transaksi berhasil (commit)
        event(new BookBorrowed($loan));

        return $loan;
    }

    /**
     * Pengembalian buku dengan transaction.
     * Menghitung denda otomatis menggunakan Value Object Denda.
     */
    public function returnLoan(Loan $loan): Loan
    {
        [$loan, $fineAmount] = DB::transaction(function () use ($loan) {
            $returnedAt = now();

            $loan->items()->whereNull('returned_at')->update(['returned_at' => $returnedAt]);

            $loan->update([
                'status' => 'returned',
                'returned_at' => $returnedAt,
            ]);

            // Denda dihitung otomatis oleh Value Object Denda
            $fine = Fine::fromDates($loan->due_at, $returnedAt);

            $fineAmount = 0.0;
            if ($fine->hasFine()) {
                FineModel::create([
                    'loan_id' => $loan->id,
                    'days_late' => $fine->getDaysLate(),
                    'daily_rate' => $fine->getDailyRate(),
                    'amount' => $fine->getAmount(),
                ]);
                $fineAmount = $fine->getAmount();
            }

            // Kembalikan status anggota menjadi aktif jika tidak ada peminjaman aktif lain
            $hasOtherActiveLoans = Loan::where('member_id', $loan->member_id)
                ->where('status', 'borrowed')
                ->where('id', '!=', $loan->id)
                ->exists();

            $this->memberService->refreshStatusAfterReturn($loan->member_id, $hasOtherActiveLoans);

            return [$loan->load('items', 'fines'), $fineAmount];
        });

        event(new BookReturned($loan, $fineAmount));

        return $loan;
    }
}
