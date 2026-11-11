<?php

namespace Modules\Membership\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Aggregate root dari Membership Context (Anggota).
 * Soft deletes diterapkan sesuai tugas.
 */
class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): \Database\Factories\MemberFactory
    {
        return \Database\Factories\MemberFactory::new();
    }

    protected $fillable = ['user_id', 'member_number', 'phone', 'address', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Keanggotaan (entity dalam aggregate Member). */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'member_role');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /* ---------------- Query Scopes ---------------- */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        return $query->when($keyword, fn (Builder $q) => $q->whereHas(
            'user',
            fn (Builder $u) => $u->where('name', 'like', "%{$keyword}%")
        )->orWhere('member_number', 'like', "%{$keyword}%"));
    }
}
