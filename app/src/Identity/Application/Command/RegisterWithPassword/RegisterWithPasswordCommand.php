<?php

namespace App\Identity\Application\Command\RegisterWithPassword;

final class RegisterWithPasswordCommand
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
