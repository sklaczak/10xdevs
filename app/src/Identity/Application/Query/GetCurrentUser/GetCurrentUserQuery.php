<?php

namespace App\Identity\Application\Query\GetCurrentUser;

final class GetCurrentUserQuery
{
    public function __construct(public int $userAccountId) {}
}
