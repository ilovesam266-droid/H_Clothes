<?php

namespace App\Enums;

enum UserSex:int
{
    case FEMALE = 0;
    case MALE = 1;

    public function label(): string{
        return match($this) {
            self::FEMALE => 'Female',
            self::MALE => 'Male',
        };
    }
}
