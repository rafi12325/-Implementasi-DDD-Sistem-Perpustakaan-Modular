<?php

namespace Modules\Circulation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Shared\ValueObjects\Fine as FineVO;

class Fine extends Model
{
    protected $fillable = ['loan_id', 'days_late', 'daily_rate', 'amount', 'paid_at'];

    protected $casts = [
        'days_late' => 'integer',
        'daily_rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /** Kembalikan sebagai Value Object Denda. */
    public function toValueObject(): FineVO
    {
        return new FineVO($this->days_late, (float) $this->daily_rate);
    }
}
