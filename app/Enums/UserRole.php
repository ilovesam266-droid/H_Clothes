<?php

namespace App\Enums;

enum UserRole: int
{
    case ADMIN = 0;
    case USER = 1;

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => "Admin",
            self::USER => "User",
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::ADMIN => "primary", // xanh dương
            self::USER => "secondary", // xám };
        };
    }
}
