<?php

namespace App\Person\Infrastructure\ReadModel;

use App\Person\Application\DTO\PersonDTO;

final class PersonDTOBuilder
{
    public function fromRow(array $row): PersonDTO
    {
        return new PersonDTO(
            (int) $row['id'],
            (string) $row['fullname'],
            $row['email'] !== null ? (string) $row['email'] : null,
            $row['phone'] !== null ? (string) $row['phone'] : null,
        );
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return PersonDTO[]
     */
    public function fromRows(array $rows): array
    {
        return array_map(fn(array $r) => $this->fromRow($r), $rows);
    }
}
