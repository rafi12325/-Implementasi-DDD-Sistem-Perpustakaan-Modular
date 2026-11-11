<?php

namespace Modules\Shared\ValueObjects;

use InvalidArgumentException;

/**
 * Value Object ISBN dengan validasi format (ISBN-10 dan ISBN-13).
 * Immutable: sekali dibuat, nilainya tidak dapat diubah.
 */
class Isbn
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = str_replace(['-', ' '], '', strtoupper($value));

        if (! $this->isValid($normalized)) {
            throw new InvalidArgumentException("Format ISBN tidak valid: {$value}");
        }

        $this->value = $normalized;
    }

    private function isValid(string $isbn): bool
    {
        return match (strlen($isbn)) {
            10 => $this->isValidIsbn10($isbn),
            13 => $this->isValidIsbn13($isbn),
            default => false,
        };
    }

    private function isValidIsbn10(string $isbn): bool
    {
        if (! preg_match('/^[0-9]{9}[0-9X]$/', $isbn)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $digit = $isbn[$i] === 'X' ? 10 : (int) $isbn[$i];
            $sum += $digit * (10 - $i);
        }

        return $sum % 11 === 0;
    }

    private function isValidIsbn13(string $isbn): bool
    {
        if (! preg_match('/^[0-9]{13}$/', $isbn)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $isbn[$i] * ($i % 2 === 0 ? 1 : 3);
        }

        return $sum % 10 === 0;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getFormatted(): string
    {
        if (strlen($this->value) === 13) {
            return sprintf('%s-%s-%s-%s-%s',
                substr($this->value, 0, 3),
                substr($this->value, 3, 1),
                substr($this->value, 4, 4),
                substr($this->value, 8, 4),
                substr($this->value, 12, 1),
            );
        }

        return sprintf('%s-%s-%s-%s',
            substr($this->value, 0, 1),
            substr($this->value, 1, 4),
            substr($this->value, 5, 4),
            substr($this->value, 9, 1),
        );
    }

    public function equals(Isbn $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
