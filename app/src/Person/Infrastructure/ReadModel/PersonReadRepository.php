<?php

namespace App\Person\Infrastructure\ReadModel;

use App\Person\Application\DTO\PersonDTO;
use Doctrine\DBAL\Connection;

final class PersonReadRepository
{
    public function __construct(
        private Connection $db,
        private PersonSqlBuilder $sqlBuilder,
        private PersonDTOBuilder $dtoBuilder,
    ) {}

    /**
     * @return PersonDTO[]
     */
    public function list(?string $search = null, int $limit = 50, int $offset = 0): array
    {
        $q = $this->sqlBuilder->buildList($search, $limit, $offset);

        $rows = $this->db
            ->executeQuery($q['sql'], $q['params'], $q['types'])
            ->fetchAllAssociative();

        return $this->dtoBuilder->fromRows($rows);
    }

    public function get(int $id): ?PersonDTO
    {
        $q = $this->sqlBuilder->buildGet($id);

        $row = $this->db->fetchAssociative($q['sql'], $q['params'], $q['types']);
        if (!$row) {
            return null;
        }

        return $this->dtoBuilder->fromRow($row);
    }

    /**
     * @return array<int, string> id => displayName
     */
    public function forSelect(?string $search = null, int $limit = 50): array
    {
        $q = $this->sqlBuilder->buildForSelect($search, $limit);

        $rows = $this->db
            ->executeQuery($q['sql'], $q['params'], $q['types'])
            ->fetchAllAssociative();

        $out = [];
        foreach ($rows as $r) {
            $out[(int) $r['id']] = (string) $r['full_name'];
        }

        return $out;
    }
}
