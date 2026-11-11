<?php

namespace Modules\Membership\Services;

use Modules\Membership\Models\Member;
use Modules\Shared\Contracts\MemberServiceInterface;

class MemberService implements MemberServiceInterface
{
    public function getMemberForLoan(int $memberId): ?array
    {
        $member = Member::find($memberId);

        if (! $member) {
            return null;
        }

        return [
            'id' => $member->id,
            'member_number' => $member->member_number,
            'name' => $member->user?->name,
            'status' => $member->status,
        ];
    }

    public function markAsBorrowing(int $memberId): void
    {
        Member::where('id', $memberId)->update(['status' => 'borrowing']);
    }

    public function refreshStatusAfterReturn(int $memberId, bool $hasOtherActiveLoans = false): void
    {
        $member = Member::find($memberId);

        if (! $member) {
            return;
        }

        // Status tetap 'borrowing' jika masih ada peminjaman aktif lain
        $member->update(['status' => $hasOtherActiveLoans ? 'borrowing' : 'active']);
    }
}
