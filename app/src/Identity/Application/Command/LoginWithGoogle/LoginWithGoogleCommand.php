<?php

namespace App\Identity\Application\Command\LoginWithGoogle;

final class LoginWithGoogleCommand
{
    public function __construct(public string $idToken) {}
}
