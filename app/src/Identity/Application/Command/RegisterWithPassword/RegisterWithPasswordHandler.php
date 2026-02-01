<?php

namespace App\Identity\Application\Command\RegisterWithPassword;

use App\Identity\Application\Port\PersonLookup;
use App\Identity\Domain\Entity\AuthMethod;
use App\Identity\Domain\Entity\PasswordCredential;
use App\Identity\Domain\Entity\UserAccount;
use App\Identity\Domain\Enum\AuthMethodType;
use App\Identity\Domain\Exception\AuthMethodAlreadyExists;
use App\Identity\Domain\Repository\AuthMethodRepository;
use App\Identity\Domain\Repository\PasswordCredentialRepository;
use App\Identity\Domain\Repository\UserAccountRepository;
use App\Identity\Infrastructure\Security\PasswordHashUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[AsMessageHandler]
final class RegisterWithPasswordHandler
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
    public function __invoke(RegisterWithPasswordCommand $cmd): int
    {
        $email = mb_strtolower(trim($cmd->email));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException('Niepoprawny email.');
        }
        if (trim($cmd->password) === '' || mb_strlen($cmd->password) < 8) {
            throw new \DomainException('Hasło musi mieć co najmniej 8 znaków.');
        }

        $existing = $this->methods->findByTypeAndIdentifier(AuthMethodType::PASSWORD, $email);
        if ($existing) {
            throw AuthMethodAlreadyExists::for(AuthMethodType::PASSWORD, $email);
        }

        $personId = $this->personLookup->findPersonIdByEmail($email);

        $account = UserAccount::create($personId);
        $this->accounts->add($account);
        $this->em->flush(); // id

        // zapisujemy metodę password: identifier=email
        $method = AuthMethod::create((int) $account->id(), AuthMethodType::PASSWORD, $email);
        $this->methods->add($method);

        // hash hasła — potrzebujemy obiektu implementującego PasswordAuthenticatedUserInterface.
        $hash = $this->hasher->hashPassword(new PasswordHashUser(null), $cmd->password);

        $cred = PasswordCredential::create((int) $account->id(), $hash);
        $this->passwordCreds->add($cred);

        $account->markLoggedIn();
        $this->em->flush();

        return (int) $account->id();
    }
}
