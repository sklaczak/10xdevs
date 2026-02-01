<?php

namespace App\Person\Application\Command\DeletePerson;

use App\Person\Domain\Repository\PersonRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeletePersonHandler
{
    public function __construct(
        private PersonRepositoryInterface $personRepository,
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(DeletePersonCommand $cmd): void
    {
        $person = $this->personRepository->get($cmd->id);

        $this->personRepository->remove($person);
        $this->em->flush();
    }
}
