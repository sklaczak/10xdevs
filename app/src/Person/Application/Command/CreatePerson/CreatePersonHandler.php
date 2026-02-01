<?php

namespace App\Person\Application\Command\CreatePerson;

use App\Person\Domain\Entity\Person;
use App\Person\Domain\Repository\PersonRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreatePersonHandler
{
    public function __construct(
        private PersonRepositoryInterface $personRepository,
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(CreatePersonCommand $cmd): int
    {
        $person = Person::create(
            $cmd->getFullName(),
            $cmd->getEmail(),
            $cmd->getPhone()
        );

        $this->personRepository->add($person);
        $this->em->flush();

        return (int) $person->id();
    }
}
