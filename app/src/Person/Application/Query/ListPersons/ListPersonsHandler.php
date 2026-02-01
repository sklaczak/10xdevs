<?php

namespace App\Person\Application\Query\ListPersons;

use App\Person\Application\DTO\PersonDTO;
use App\Person\Infrastructure\ReadModel\PersonReadRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ListPersonsHandler
{
    public function __construct(private PersonReadRepository $readRepo) {}

    /** @return PersonDTO[] */
    public function __invoke(ListPersonsQuery $q): array
    {
        return $this->readRepo->list($q->search, $q->limit, $q->offset);
    }
}
