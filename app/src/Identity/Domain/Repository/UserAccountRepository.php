<?php

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\UserAccount;

interface UserAccountRepository
{
    public function add(UserAccount $account): void;

    public function get(int $id): UserAccount;
    public function find(int $id): ?UserAccount;

    /**
     * Znajdź konto po podpiętym Person.
     */
    public function findByPersonId(int $personId): ?UserAccount;
}
