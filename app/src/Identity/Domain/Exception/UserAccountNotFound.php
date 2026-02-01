<?php

namespace App\Identity\Domain\Exception;

final class UserAccountNotFound extends IdentityException
{
    public static function byId(int $id): self
    {
        return new self('Nie znaleziono konta. ID: '.$id);
    }
}
