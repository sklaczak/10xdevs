<?php

namespace App\Identity\Application\Query\GetCurrentUser;

use App\Identity\Application\DTO\CurrentUserDTO;
use App\Identity\Domain\Repository\UserAccountRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetCurrentUserHandler
{
    public function __construct(private UserAccountRepository $accounts) {}

    public function __invoke(GetCurrentUserQuery $q): CurrentUserDTO
    {
        $acc = $this->accounts->get($q->userAccountId);

        return new CurrentUserDTO(
            userAccountId: (int) $acc->id(),
            personId: $acc->personId(),
            roles: $acc->roles(),
            enabled: $acc->enabled(),
        );
    }
}
