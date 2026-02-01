<?php


namespace App\Person\Domain\Repository;

use App\Person\Domain\Entity\Person;

interface PersonRepositoryInterface
{
    public function get(int $id): Person;

    public function add(Person $person): void;

    public function remove(Person $person): void;
}
