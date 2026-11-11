<?php

namespace Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use Modules\Catalog\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Daftar buku dengan EAGER LOADING (author, publisher, categories)
     * untuk menghindari N+1 query, plus query scopes search & byCategory.
     */
    public function index(\Illuminate\Http\Request $request): View
    {
        $books = Book::query()
            ->with(['author', 'publisher', 'categories']) // eager loading
            ->available()                                  // scope: buku tersedia
            ->search($request->query('q'))                 // scope: pencarian
            ->byCategory((int) $request->query('category')) // scope: filter kategori
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('catalog::books.index', compact('books'));
    }

    /** Soft delete buku (admin). */
    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return back()->with('status', "Buku '{$book->title}' dipindahkan ke sampah (soft delete).");
    }

    /** Restore buku yang di-soft delete (admin). */
    public function restore(int $id): RedirectResponse
    {
        $book = Book::onlyTrashed()->findOrFail($id);
        $book->restore();

        return back()->with('status', "Buku '{$book->title}' berhasil di-restore.");
    }
}
