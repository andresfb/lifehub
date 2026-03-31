<?php

namespace App\Enums;

enum AccountType: string
{
    case OWNER = 'owner';
    case USER = 'user';
}
