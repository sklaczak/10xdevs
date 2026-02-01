<?php

namespace App\Identity\Infrastructure\Security;

use App\Identity\Domain\Repository\UserAccountRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface
{
    public function __construct(private UserAccountRepository $accounts) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $id = (int) $identifier;

        $acc = $this->accounts->find($id);
        if (!$acc) {
            throw new UserNotFoundException();
        }

        return new UserAccountUser((int) $acc->id(), $acc->roles(), $acc->enabled());
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === UserAccountUser::class;
    }
}
