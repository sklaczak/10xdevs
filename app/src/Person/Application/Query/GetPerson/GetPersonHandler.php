<?php

namespace App\Person\Application\Query\GetPerson;

use App\Person\Application\DTO\PersonDTO;
use App\Person\Infrastructure\ReadModel\PersonReadRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetPersonHandler
{
    public function __construct(private PersonReadRepository $readRepo) {}

    public function __invoke(GetPersonQuery $q): PersonDTO
    {
        $dto = $this->readRepo->get($q->id);
        if (!$dto) {
            throw new \RuntimeException('Person nie znaleziony: '.$q->id);
        }
        return $dto;
    }
}
