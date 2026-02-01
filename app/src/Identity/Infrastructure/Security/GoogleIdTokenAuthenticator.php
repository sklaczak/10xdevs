<?php

namespace App\Identity\Infrastructure\Security;

use App\Identity\Application\Command\LoginWithGoogle\LoginWithGoogleCommand;
use App\Identity\Domain\Repository\UserAccountRepository;
use App\Shared\Application\Bus\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class GoogleIdTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private CommandBus $commandBus,
        private UserAccountRepository $accounts,
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/auth/google' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $data = json_decode((string) $request->getContent(), true) ?: [];
        $idToken = (string) ($data['idToken'] ?? '');

        if ($idToken === '') {
            throw new AuthenticationException('Brak idToken.');
        }

        $userAccountId = (int) $this->commandBus->dispatch(new LoginWithGoogleCommand($idToken));

        return new SelfValidatingPassport(
            new UserBadge((string) $userAccountId, function (string $identifier) {
                $acc = $this->accounts->get((int) $identifier);
                return new UserAccountUser((int) $acc->id(), $acc->roles(), $acc->enabled());
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response
    {
        return new JsonResponse(['ok' => true]);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['ok' => false, 'error' => $exception->getMessage()], 401);
    }
}
