<?php

namespace App\Person\Infrastructure\ReadModel;

use Doctrine\DBAL\ParameterType;

final class PersonSqlBuilder
{
    /**
     * @return array{sql: string, params: array<string, mixed>, types: array<string, mixed>}
     */
    public function buildList(?string $search, int $limit, int $offset): array
    {
        $sql = <<<SQL
            SELECT
                id,
                full_name AS fullName,
                email,
                phone
            FROM person
            WHERE 1=1
        SQL;

        $params = [];
        $types  = [];

        $this->applySearch($sql, $params, $types, $search);

        $sql .= ' ORDER BY full_name ASC LIMIT :limit OFFSET :offset';

        $params['limit']  = $limit;
        $params['offset'] = $offset;

        $types['limit']  = ParameterType::INTEGER;
        $types['offset'] = ParameterType::INTEGER;

        return ['sql' => $sql, 'params' => $params, 'types' => $types];
    }

    /**
     * @return array{sql: string, params: array<string, mixed>, types: array<string, mixed>}
     */
    public function buildGet(int $id): array
    {
        return [
            'sql' => 'SELECT id, full_name AS fullName, email, phone FROM person WHERE id = :id',
            'params' => ['id' => $id],
            'types' => ['id' => ParameterType::INTEGER],
        ];
    }

    /**
     * @return array{sql: string, params: array<string, mixed>, types: array<string, mixed>}
     */
    public function buildForSelect(?string $search, int $limit): array
    {
        $sql = 'SELECT id, full_name FROM person';
        $params = [];
        $types  = [];

        if ($search !== null && trim($search) !== '') {
            $sql .= ' WHERE LOWER(full_name) LIKE :q';
            $params['q'] = '%'.mb_strtolower(trim($search)).'%';
            $types['q']  = ParameterType::STRING;
        }

        $sql .= ' ORDER BY full_name ASC LIMIT :limit';
        $params['limit'] = $limit;
        $types['limit']  = ParameterType::INTEGER;

        return ['sql' => $sql, 'params' => $params, 'types' => $types];
    }

    private function applySearch(string &$sql, array &$params, array &$types, ?string $search): void
    {
        if ($search === null || trim($search) === '') {
            return;
        }

        $sql .= ' AND (LOWER(full_name) LIKE :q OR LOWER(email) LIKE :q)';
        $params['q'] = '%'.mb_strtolower(trim($search)).'%';
        $types['q']  = ParameterType::STRING;
    }
}
