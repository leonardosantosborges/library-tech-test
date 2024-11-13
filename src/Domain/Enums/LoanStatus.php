<?php

namespace Domain\Enums;

class LoanStatus
{
    const BORROWED = 'borrowed';
    const RETURNED = 'returned';
    const OVERDUE = 'overdue';

    public static function from(string $value): self
    {
        return match ($value) {
            "borrowed" => new self(self::BORROWED),
            self::RETURNED => new self(self::RETURNED),
            self::OVERDUE => new self(self::OVERDUE),
            default => throw new \ValueError("Invalid loan status: $value"),
        };
    }
}
