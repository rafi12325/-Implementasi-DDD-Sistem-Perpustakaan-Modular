<?php

use Illuminate\Support\Facades\Event;
use Modules\Catalog\Models\Book;
use Modules\Catalog\Models\Category;
use Modules\Circulation\Events\BookBorrowed;
use Modules\Circulation\Models\Loan;
use Modules\Circulation\Services\LoanService;
use Modules\Membership\Models\Member;
use Modules\Shared\Exceptions\InsufficientStockException;
use Modules\Shared\ValueObjects\Fine;
use Modules\Shared\ValueObjects\Isbn;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

/* ---------------- Value Object ISBN ---------------- */

test('isbn value object menerima isbn-13 valid dan menolak yang invalid', function () {
    $isbn = new Isbn('978-602-03-1234-7');
    expect((string) $isbn)->toBe('9786020312347');

    new Isbn('9786020312340'); // checksum salah
})->throws(InvalidArgumentException::class);

test('isbn value object menolak format acak', function () {
    new Isbn('ABC123');
})->throws(InvalidArgumentException::class);

test('book cast isbn menjadi value object', function () {
    $book = Book::first();
    expect($book->isbn)->toBeInstanceOf(Isbn::class);
});

/* ---------------- Value Object Denda ---------------- */

test('denda value object menghitung otomatis', function () {
    $fine = new Fine(3);
    expect($fine->getAmount())->toBe(3000.0)
        ->and($fine->hasFine())->toBeTrue();

    $noFine = new Fine(0);
    expect($noFine->getAmount())->toBe(0.0)
        ->and($noFine->hasFine())->toBeFalse();
});

test('denda value object fromdates menghitung keterlambatan', function () {
    $due = now()->subDays(5);
    $fine = Fine::fromDates($due, now());

    expect($fine->getDaysLate())->toBeGreaterThanOrEqual(4);

    $onTime = Fine::fromDates(now()->addDays(3), now());
    expect($onTime->hasFine())->toBeFalse();
});

/* ---------------- Domain Event ---------------- */

test('event bookborrowed ditembakkan setelah peminjaman berhasil', function () {
    Event::fake([BookBorrowed::class]);

    $member = Member::where('status', 'active')->first();
    $book = Book::available()->first();

    app(LoanService::class)->borrow($member->id, [['book_id' => $book->id, 'quantity' => 1]]);

    Event::assertDispatched(BookBorrowed::class, 1);
});

/* ---------------- Database Transaction ---------------- */

test('peminjaman berhasil mengurangi stok dan update status anggota', function () {
    $member = Member::where('status', 'active')->first();
    $book = Book::where('stock', '>=', 2)->where('is_active', true)->first();

    $stockBefore = $book->stock;

    $loan = app(LoanService::class)->borrow($member->id, [['book_id' => $book->id, 'quantity' => 2]]);

    expect($book->fresh()->stock)->toBe($stockBefore - 2)
        ->and($loan->items)->toHaveCount(1)
        // Snapshot buku tersimpan di DetailPeminjaman
        ->and($loan->items->first()->book_title)->toBe($book->title)
        ->and($loan->items->first()->book_isbn)->toBe((string) $book->isbn)
        ->and($member->fresh()->status)->toBe('borrowing');
});

test('transaction rollback saat stok tidak mencukupi', function () {
    $member = Member::where('status', 'active')->first();
    $book = Book::where('stock', '>', 0)->where('stock', '<', 10)->first();
    $stockBefore = $book->stock;
    $loanCountBefore = Loan::count();

    try {
        app(LoanService::class)->borrow($member->id, [
            ['book_id' => $book->id, 'quantity' => $stockBefore + 5],
        ]);
        $this->fail('Seharusnya melempar InsufficientStockException');
    } catch (InsufficientStockException) {
        // expected
    }

    // Semua perubahan harus di-rollback
    expect($book->fresh()->stock)->toBe($stockBefore)
        ->and(Loan::count())->toBe($loanCountBefore)
        ->and($member->fresh()->status)->toBe('active');
});

test('pengembalian terlambat menghasilkan denda otomatis', function () {
    $member = Member::where('status', 'active')->first();
    $book = Book::available()->first();

    $loan = app(LoanService::class)->borrow($member->id, [['book_id' => $book->id, 'quantity' => 1]]);

    // Paksa agar sudah lewat jatuh tempo
    $loan->update(['due_at' => now()->subDays(4)]);

    $returned = app(LoanService::class)->returnLoan($loan->fresh());

    expect($returned->status)->toBe('returned')
        ->and($returned->fines)->toHaveCount(1)
        ->and((float) $returned->fines->first()->amount)->toBe(4000.0) // 4 hari x 1000
        ->and($member->fresh()->status)->toBe('active');
});

/* ---------------- Soft Deletes ---------------- */

test('soft delete dan restore buku', function () {
    $book = Book::available()->first();
    $id = $book->id;

    $book->delete();

    expect(Book::find($id))->toBeNull()
        ->and(Book::withTrashed()->find($id))->not->toBeNull();

    Book::onlyTrashed()->find($id)->restore();

    expect(Book::find($id))->not->toBeNull();
});

test('soft delete dan restore anggota', function () {
    $member = Member::first();
    $id = $member->id;

    $member->delete();

    expect(Member::find($id))->toBeNull()
        ->and(Member::withTrashed()->find($id))->not->toBeNull();

    Member::onlyTrashed()->find($id)->restore();

    expect(Member::find($id))->not->toBeNull();
});

test('soft delete dan restore peminjaman', function () {
    $member = Member::where('status', 'active')->first();
    $book = Book::available()->first();

    $loan = app(LoanService::class)->borrow($member->id, [['book_id' => $book->id, 'quantity' => 1]]);
    $loan->delete();

    expect(Loan::find($loan->id))->toBeNull();

    Loan::onlyTrashed()->find($loan->id)->restore();

    expect(Loan::find($loan->id))->not->toBeNull();
});

/* ---------------- Relasi & Query Scopes ---------------- */

test('relasi many-to-many dengan pivot added_at dan self-referencing kategori', function () {
    $book = Book::with('categories')->first();

    expect($book->categories)->not->toBeEmpty()
        ->and($book->categories->first()->pivot->added_at)->not->toBeNull();

    $parent = Category::whereNull('parent_id')->first();
    $child = Category::factory()->create(['parent_id' => $parent->id]);

    expect($child->parent->id)->toBe($parent->id)
        ->and($parent->children->pluck('id'))->toContain($child->id);
});

test('query scopes memfilter buku dengan benar', function () {
    $availableBooks = Book::available()->get();
    expect($availableBooks->isNotEmpty())->toBeTrue();
    foreach ($availableBooks as $b) {
        expect($b->stock)->toBeGreaterThan(0);
    }

    $root = Category::with('children')->first();
    if ($root->children->isNotEmpty()) {
        // Filter kategori mencakup sub-kategori
        $results = Book::byCategory($root->id)->get();
        expect($results->isNotEmpty())->toBeTrue();
    }
});
