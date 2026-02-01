<?php

namespace App\Identity\Infrastructure\Doctrine;

use App\Identity\Domain\Entity\UserAccount;
use App\Identity\Domain\Exception\UserAccountNotFound;
use App\Identity\Domain\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserAccountRepository implements UserAccountRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(UserAccount $account): void
    {
        $this->em->persist($account);
    }

    public function get(int $id): UserAccount
    {
        $acc = $this->em->find(UserAccount::class, $id);
        if (!$acc) {
            throw UserAccountNotFound::byId($id);
        }
        return $acc;
    }

    public function find(int $id): ?UserAccount
    {
        return $this->em->find(UserAccount::class, $id);
    }

    public function findByPersonId(int $personId): ?UserAccount
    {
        return $this->em->getRepository(UserAccount::class)->findOneBy(['personId' => $personId]);
    }
}
