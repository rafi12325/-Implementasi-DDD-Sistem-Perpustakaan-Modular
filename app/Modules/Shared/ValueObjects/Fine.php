<?php

namespace Modules\Shared\ValueObjects;

use InvalidArgumentException;

/**
 * Value Object Denda dengan perhitungan otomatis.
 * Denda dihitung dari jumlah hari keterlambatan dikali tarif harian.
 */
class Fine
{
    public const DAILY_RATE = 1000; // tarif default denda per hari (IDR)

    private int $daysLate;

    private float $dailyRate;

    private float $amount;

    public function __construct(int $daysLate, float $dailyRate = self::DAILY_RATE)
    {
        if ($daysLate < 0) {
            throw new InvalidArgumentException('Hari keterlambatan tidak boleh negatif');
        }

        if ($dailyRate < 0) {
            throw new InvalidArgumentException('Tarif denda tidak boleh negatif');
        }

        $this->daysLate = $daysLate;
        $this->dailyRate = $dailyRate;
        // Perhitungan otomatis: denda hanya berlaku jika terlambat
        $this->amount = max(0, $daysLate) * $dailyRate;
    }

    /**
     * Buat Denda dari tanggal jatuh tempo dan tanggal pengembalian.
     */
    public static function fromDates(\DateTimeInterface $dueDate, ?\DateTimeInterface $returnedAt = null, float $dailyRate = self::DAILY_RATE): self
    {
        $returnedAt ??= new \DateTimeImmutable();
        $daysLate = (int) floor($dueDate->diff($returnedAt)->days);

        // Jika pengembalian masih sebelum jatuh tempo, tidak ada denda
        if ($returnedAt < $dueDate) {
            $daysLate = 0;
        }

        return new self($daysLate, $dailyRate);
    }

    public function getDaysLate(): int
    {
        return $this->daysLate;
    }

    public function getDailyRate(): float
    {
        return $this->dailyRate;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function hasFine(): bool
    {
        return $this->amount > 0;
    }

    public function equals(Fine $other): bool
    {
        return $this->amount === $other->amount && $this->dailyRate === $other->dailyRate;
    }

    public function __toString(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
