<?php

namespace App\Identity\Infrastructure\Person;

use App\Identity\Application\Port\PersonLookup;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;

final class DbalPersonLookup implements PersonLookup
{
    public function __construct(private Connection $db) {}

    public function findPersonIdByEmail(string $email): ?int
    {
        $email = mb_strtolower(trim($email));
        if ($email === '') {
            return null;
        }

        $id = $this->db->fetchOne(
            'SELECT id FROM person WHERE LOWER(email) = :email LIMIT 1',
            ['email' => $email],
            ['email' => ParameterType::STRING]
        );

        return $id !== false ? (int) $id : null;
    }
}
