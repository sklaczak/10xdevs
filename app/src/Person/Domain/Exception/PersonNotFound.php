<?php

namespace App\Person\Domain\Exception;

use App\Shared\Domain\Exception\NotFoundException;

final class PersonNotFound extends NotFoundException
{
    public static function byId(int $id): self
    {
        return new self('Person nie znaleziony. ID: '.$id);
    }
}
