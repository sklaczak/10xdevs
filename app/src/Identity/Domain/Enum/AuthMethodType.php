<?php

namespace App\Identity\Domain\Enum;

final class AuthMethodType
{
    public const PASSWORD = 'password';
    public const GOOGLE_OIDC = 'google_oidc';
    public const MOBILE_OIDC = 'mobile_oidc'; // później
    public const PASSKEY = 'passkey';         // później
}
