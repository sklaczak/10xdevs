<?php

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\AuthMethod;

interface AuthMethodRepository
{
    public function add(AuthMethod $method): void;

    public function findByTypeAndIdentifier(string $type, string $identifier): ?AuthMethod;

    /**
     * @return AuthMethod[]
     */
    public function listByUserAccountId(int $userAccountId): array;
}
