<?php

namespace App\Person\Application\Query\PersonsForSelect;

final class PersonsForSelectQuery
{
    public function __construct(
        public ?string $search = null,
        public int $limit = 50,
    ) {}
}
