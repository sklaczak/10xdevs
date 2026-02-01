<?php

namespace App\Identity\Application\Port;

interface PersonLookup
{
    /**
     * Zwraca ID Person dla podanego emaila, albo null jeśli brak.
     */
    public function findPersonIdByEmail(string $email): ?int;
}
