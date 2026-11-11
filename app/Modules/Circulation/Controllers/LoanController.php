<?php

namespace Modules\Circulation\Controllers;

use App\Http\Controllers\Controller;
use Modules\Catalog\Models\Book;
use Modules\Circulation\Models\Loan;
use Modules\Circulation\Services\LoanService;
use Modules\Membership\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    /**
     * Daftar peminjaman dengan EAGER LOADING (items, member, fines)
     * untuk menghindari N+1 query.
     */
    public function index(Request $request): View
    {
        $loans = Loan::query()
            ->with(['items', 'member.user', 'fines']) // eager loading
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest('borrowed_at')
            ->paginate(10)
            ->withQueryString();

        return view('circulation::loans.index', compact('loans'));
    }

    /** Form peminjaman baru: buku tersedia + anggota aktif (eager loaded). */
    public function create(): View
    {
        $books = Book::with(['author', 'publisher'])->available()->orderBy('title')->get();
        $members = Member::with('user')->active()->orderBy('member_number')->get();

        return view('circulation::loans.create', compact('books', 'members'));
    }

    /** Simpan peminjaman (transaction di LoanService). */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'integer', 'exists:members,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.book_id' => ['nullable', 'integer', 'exists:books,id'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1', 'max:5'],
            'loan_days' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        // Hanya proses item yang dicentang pada form
        $items = collect($request->input('items', []))
            ->filter(fn ($item) => is_array($item) && ! empty($item['checked']))
            ->map(fn ($item) => [
                'book_id' => (int) ($item['book_id'] ?? 0),
                'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
            ])
            ->filter(fn ($item) => $item['book_id'] > 0)
            ->values()
            ->all();

        if (empty($items)) {
            return back()->withErrors(['items' => 'Pilih minimal satu buku yang ingin dipinjam.'])->withInput();
        }

        try {
            $loan = $this->loanService->borrow(
                $validated['member_id'],
                $items,
                $validated['loan_days'] ?? 7
            );
        } catch (\Modules\Shared\Exceptions\InsufficientStockException $e) {
            // Transaction sudah di-rollback oleh LoanService
            return back()->withErrors(['stock' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('loans.show', $loan)
            ->with('status', "Peminjaman {$loan->loan_number} berhasil dibuat.");
    }

    public function show(Loan $loan): View
    {
        $loan->load(['items', 'member.user', 'fines']);

        return view('circulation::loans.show', compact('loan'));
    }

    /** Pengembalian buku (transaction + denda otomatis). */
    public function returnLoan(Loan $loan): RedirectResponse
    {
        $loan = $this->loanService->returnLoan($loan);

        $fine = $loan->fines->sum('amount');

        return back()->with(
            'status',
            $fine > 0
                ? "Buku dikembalikan. Denda keterlambatan: Rp " . number_format($fine, 0, ',', '.')
                : 'Buku dikembalikan tepat waktu, tidak ada denda.'
        );
    }

    /** Soft delete peminjaman (admin). */
    public function destroy(Loan $loan): RedirectResponse
    {
        $loan->delete();

        return back()->with('status', "Peminjaman {$loan->loan_number} dihapus sementara (soft delete).");
    }

    /** Restore peminjaman yang di-soft delete (admin). */
    public function restore(int $id): RedirectResponse
    {
        $loan = Loan::onlyTrashed()->findOrFail($id);
        $loan->restore();

        return back()->with('status', "Peminjaman {$loan->loan_number} berhasil di-restore.");
    }

    /** Daftar peminjaman yang terhapus (untuk restore admin). */
    public function trashed(): View
    {
        $loans = Loan::onlyTrashed()->with(['items', 'member.user'])->latest()->paginate(10);

        return view('circulation::loans.trashed', compact('loans'));
    }
}
