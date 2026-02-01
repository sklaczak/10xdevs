<?php


namespace App\Person\Application\Command\UpdatePerson;

use App\Person\Domain\Repository\PersonRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdatePersonHandler
{
    public function __construct(
        private PersonRepositoryInterface $personRepository,
        private EntityManagerInterface $em,
    )
    {
    }

    public function __invoke(UpdatePersonCommand $cmd): void
    {
        $person = $this->personRepository->get($cmd->id);

        $person->rename($cmd->fullName);
        $person->changeContact($cmd->email, $cmd->phone);

        $this->em->flush();
    }
}
