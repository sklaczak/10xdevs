<?php

namespace App\Identity\Application\Command\LoginWithPassword;

use App\Identity\Application\Port\PersonLookup;
use App\Identity\Domain\Enum\AuthMethodType;
use App\Identity\Domain\Repository\AuthMethodRepository;
use App\Identity\Domain\Repository\PasswordCredentialRepository;
use App\Identity\Domain\Repository\UserAccountRepository;
use App\Identity\Infrastructure\Security\PasswordHashUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[AsMessageHandler]
final class LoginWithPasswordHandler
{
    public function __construct(
        private UserAccountRepository $accounts,
        private AuthMethodRepository $methods,
        private PasswordCredentialRepository $passwordCreds,
        private PersonLookup $personLookup,
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface $em,
    ) {}

    /** @return int userAccountId */
    public function __invoke(LoginWithPasswordCommand $cmd): int
    {
        $email = mb_strtolower(trim($cmd->email));
        $password = (string) $cmd->password;

        $method = $this->methods->findByTypeAndIdentifier(AuthMethodType::PASSWORD, $email);
        if (!$method) {
            throw new AuthenticationException('Niepoprawny email lub hasło.');
        }

        $cred = $this->passwordCreds->findByUserAccountId($method->userAccountId());
        if (!$cred) {
            throw new AuthenticationException('Niepoprawny email lub hasło.');
        }

        $userForCheck = new PasswordHashUser($cred->passwordHash());

        if (!$this->hasher->isPasswordValid($userForCheck, $password)) {
            throw new AuthenticationException('Niepoprawny email lub hasło.');
        }

        $account = $this->accounts->get($method->userAccountId());

        // jeśli konto nie ma Person, a w DB istnieje Person o tym emailu, podepnij
        if ($account->personId() === null) {
            $personId = $this->personLookup->findPersonIdByEmail($email);
            if ($personId !== null) {
                $account->attachPerson($personId);
            }
        }

        $method->markUsed();
        $account->markLoggedIn();
        $this->em->flush();

        return (int) $account->id();
    }
}
