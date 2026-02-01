<?php

namespace App\Identity\Infrastructure\Doctrine;

use App\Identity\Domain\Entity\AuthMethod;
use App\Identity\Domain\Repository\AuthMethodRepository;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineAuthMethodRepository implements AuthMethodRepository
{
    public function __construct(private EntityManagerInterface $em) {}

    public function add(AuthMethod $method): void
    {
        $this->em->persist($method);
    }

    public function findByTypeAndIdentifier(string $type, string $identifier): ?AuthMethod
    {
        return $this->em->getRepository(AuthMethod::class)->findOneBy([
            'type' => $type,
            'identifier' => $identifier,
        ]);
    }

    public function listByUserAccountId(int $userAccountId): array
    {
        return $this->em->getRepository(AuthMethod::class)->findBy([
            'userAccountId' => $userAccountId,
        ]);
    }
}
