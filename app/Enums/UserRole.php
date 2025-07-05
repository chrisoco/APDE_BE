<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum UserRole: string
{
    use HasEnumHelpers;

    case GUEST = 'guest';
    case USER = 'user';
    case ADMIN = 'admin';
    case SUPER_ADMIN = 'super_admin';
}
