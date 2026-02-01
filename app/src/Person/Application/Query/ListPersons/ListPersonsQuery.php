<?php

namespace App\Person\Application\Query\ListPersons;

final class ListPersonsQuery
{
    public function __construct(
        public ?string $search = null,
        public int $limit = 50,
        public int $offset = 0,
    ) {}
}
