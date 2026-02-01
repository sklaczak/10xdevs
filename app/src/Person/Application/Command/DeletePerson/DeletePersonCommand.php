<?php

namespace App\Person\Application\Command\DeletePerson;

final class DeletePersonCommand
{
    public function __construct(public int $id)
    {
    }
}
