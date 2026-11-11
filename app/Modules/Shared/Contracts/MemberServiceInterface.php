<?php

namespace Modules\Shared\Contracts;

/**
 * Contract antar bounded context: Membership -> Circulation.
 */
interface MemberServiceInterface
{
    /**
     * Ambil data anggota untuk kebutuhan peminjaman.
     */
    public function getMemberForLoan(int $memberId): ?array;

    /**
     * Perbarui status anggota setelah peminjaman berhasil.
     */
    public function markAsBorrowing(int $memberId): void;

    /**
     * Kembalikan status anggota menjadi aktif jika tidak ada
     * peminjaman aktif tersisa (dipanggil saat pengembalian).
     * Keputusan berdasarkan informasi dari Circulation Context.
     */
    public function refreshStatusAfterReturn(int $memberId, bool $hasOtherActiveLoans): void;
}
