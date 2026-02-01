<?php

namespace App\Identity\Application\Command\LoginWithGoogle;

use App\Identity\Application\Port\PersonLookup;
use App\Identity\Domain\Entity\AuthMethod;
use App\Identity\Domain\Entity\UserAccount;
use App\Identity\Domain\Enum\AuthMethodType;
use App\Identity\Domain\Repository\AuthMethodRepository;
use App\Identity\Domain\Repository\UserAccountRepository;
use App\Identity\Infrastructure\Security\GoogleOidcClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class LoginWithGoogleHandler
{
    public function __construct(
        private GoogleOidcClient $oidc,
        private UserAccountRepository $accounts,
        private AuthMethodRepository $methods,
        private PersonLookup $personLookup,
        private EntityManagerInterface $em,
    ) {}

    /** @return int userAccountId */
    public function __invoke(LoginWithGoogleCommand $cmd): int
    {
        $claims = $this->oidc->verifyIdToken($cmd->idToken);

        $sub = (string) ($claims['sub'] ?? '');
        if ($sub === '') {
            throw new \RuntimeException('Google token bez sub.');
        }

        $email = isset($claims['email']) ? (string) $claims['email'] : null;
        $email = $email !== null ? trim($email) : null;

        $method = $this->methods->findByTypeAndIdentifier(AuthMethodType::GOOGLE_OIDC, $sub);

        if ($method) {
            $account = $this->accounts->get($method->userAccountId());
            $this->attachPersonIfPossible($account, $email);

            $method->markUsed();
            $account->markLoggedIn();
            $this->em->flush();

            return (int) $account->id();
        }

        // brak auth_method => tworzymy konto
        $personId = $email ? $this->personLookup->findPersonIdByEmail($email) : null;

        $account = UserAccount::create($personId);
        $this->accounts->add($account);
        $this->em->flush(); // potrzebujemy ID

        $newMethod = AuthMethod::create(
            (int) $account->id(),
            AuthMethodType::GOOGLE_OIDC,
            $sub,
            [
                'email' => $email,
                'name' => $claims['name'] ?? null,
                'picture' => $claims['picture'] ?? null,
            ]
        );
        $this->methods->add($newMethod);

        $account->markLoggedIn();
        $this->em->flush();

        return (int) $account->id();
    }

    private function attachPersonIfPossible(UserAccount $account, ?string $email): void
    {
        if ($account->personId() !== null) {
            return;
        }
        if ($email === null || $email === '') {
            return;
        }

        $personId = $this->personLookup->findPersonIdByEmail($email);
        if ($personId !== null) {
            $account->attachPerson($personId);
        }
    }
}
