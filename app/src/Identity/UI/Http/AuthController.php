<?php

namespace App\Identity\UI\Http;

use App\Identity\Application\Command\LoginWithPassword\LoginWithPasswordCommand;
use App\Identity\Application\Command\RegisterWithPassword\RegisterWithPasswordCommand;
use App\Identity\Domain\Repository\UserAccountRepository;
use App\Identity\Infrastructure\Security\UserAccountUser;
use App\Shared\Application\Bus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    public function __construct(
        private CommandBus $commandBus,
        private UserAccountRepository $accounts,
        private Security $security,
    ) {}

    #[Route('/auth/register', name: 'auth_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode((string) $request->getContent(), true) ?: [];
        $email = (string) ($data['email'] ?? '');
        $password = (string) ($data['password'] ?? '');

        try {
            // rejestracja
            $this->commandBus->dispatch(new RegisterWithPasswordCommand($email, $password));

            // auto-login: używamy tego samego use-case co login (spójność)
            $userAccountId = (int) $this->commandBus->dispatch(new LoginWithPasswordCommand($email, $password));

            $acc = $this->accounts->get($userAccountId);
            $user = new UserAccountUser((int) $acc->id(), $acc->roles(), $acc->enabled());

            // logowanie do sesji (firewall 'main')
            $this->security->login($user, null, 'main');

            return new JsonResponse(['ok' => true, 'userAccountId' => $userAccountId], 201);
        } catch (\DomainException $e) {
            return new JsonResponse(['ok' => false, 'error' => $e->getMessage()], 400);
        }
    }
}
