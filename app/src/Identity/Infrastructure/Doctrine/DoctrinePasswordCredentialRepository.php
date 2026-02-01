<?php

namespace App\Identity\Infrastructure\Doctrine;

use App\Identity\Domain\Entity\PasswordCredential;
use App\Identity\Domain\Repository\PasswordCredentialRepository;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrinePasswordCredentialRepository implements PasswordCredentialRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(PasswordCredential $cred): void
    {
        $this->em->persist($cred);
    }

    public function findByUserAccountId(int $userAccountId): ?PasswordCredential
    {
        return $this->em->find(PasswordCredential::class, $userAccountId);
    }
}
