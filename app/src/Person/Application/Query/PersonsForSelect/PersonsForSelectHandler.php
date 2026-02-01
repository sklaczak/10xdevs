<?php

namespace App\Person\Application\Query\PersonsForSelect;

use App\Person\Infrastructure\ReadModel\PersonReadRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PersonsForSelectHandler
{
    public function __construct(private PersonReadRepository $readRepo) {}

    /** @return array<int, string> */
    public function __invoke(PersonsForSelectQuery $q): array
    {
        return $this->readRepo->forSelect($q->search, $q->limit);
    }
}
