<?php

namespace App\Identity\Application\Command\LoginWithPassword;

final class LoginWithPasswordCommand
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
