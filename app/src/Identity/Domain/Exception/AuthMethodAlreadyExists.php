<?php

namespace App\Identity\Domain\Exception;

final class AuthMethodAlreadyExists extends IdentityException
{
    public static function for(string $type, string $identifier): self
    {
        return new self(sprintf('Metoda logowania już istnieje (%s / %s).', $type, $identifier));
    }
}
