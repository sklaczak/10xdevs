<?php

namespace App\Identity\Domain\Repository;

use App\Identity\Domain\Entity\PasswordCredential;

interface PasswordCredentialRepository
{
    public function add(PasswordCredential $cred): void;

    public function findByUserAccountId(int $userAccountId): ?PasswordCredential;
}
