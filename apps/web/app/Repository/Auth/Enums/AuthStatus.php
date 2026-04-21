<?php

namespace App\Repository\Auth\Enums;

enum AuthStatus: string
{
    case SUCCESS = 'success';
    case FAILURE = 'failure';
    case TWO_FACTOR = 'two-factor';
    case VERIFY_EMAIL = 'verify-email';
}
