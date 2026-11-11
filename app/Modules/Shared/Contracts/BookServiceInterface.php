<?php

namespace Modules\Shared\Contracts;

/**
 * Contract antar bounded context: Catalog -> Circulation.
 * Circulation tidak boleh mengakses model Book secara langsung,
 * semua akses melalui contract ini.
 */
interface BookServiceInterface
{
    /**
     * Ambil snapshot data buku (judul, ISBN, stok).
     * Snapshot disimpan di DetailPeminjaman agar tidak bergantung
     * pada perubahan data buku di masa depan.
     */
    public function getBookSnapshot(int $bookId): ?array;

    /**
     * Cek apakah stok buku tersedia untuk quantity yang diminta.
     */
    public function isBookAvailable(int $bookId, int $quantity): bool;

    /**
     * Kurangi stok buku (dipanggil dalam transaction peminjaman).
     */
    public function reduceStock(int $bookId, int $quantity): bool;
}
