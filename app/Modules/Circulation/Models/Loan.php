<?php

namespace Modules\Circulation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Membership\Models\Member;

/**
 * Aggregate root dari Circulation Context (Peminjaman).
 * Soft deletes diterapkan 
 */
class Loan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'loan_number', 'member_id', 'status',
        'borrowed_at', 'due_at', 'returned_at',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /** HasMany: Peminjaman -> DetailPeminjaman (entity dalam aggregate). */
    public function items(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    /**
     * Relasi lintas context via FK. Per module: akses data anggota
     *  contract
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', 'borrowed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'borrowed')->where('due_at', '<', now());
    }

    public function isOverdue(): bool
    {
        return $this->status === 'borrowed' && $this->due_at->isPast();
    }
}
