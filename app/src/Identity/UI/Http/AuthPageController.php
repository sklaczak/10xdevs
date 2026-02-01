<?php

namespace App\Identity\UI\Http;

use App\Identity\Application\Command\LoginWithPassword\LoginWithPasswordCommand;
use App\Identity\Application\Command\RegisterWithPassword\RegisterWithPasswordCommand;
use App\Identity\Domain\Repository\UserAccountRepository;
use App\Identity\Infrastructure\Security\UserAccountUser;
use App\Shared\Application\Bus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthPageController extends AbstractController
{
    public function __construct(
        private CommandBus $commandBus,
        private UserAccountRepository $accounts,
        private Security $security,
    ) {}

    #[Route('/register', name: 'register_page', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        $error = null;

        if ($request->isMethod('POST')) {
            $email = $request->request->getString('email');
            $password = $request->request->getString('password');

            try {
                $this->commandBus->dispatch(new RegisterWithPasswordCommand($email, $password));
                $userAccountId = (int) $this->commandBus->dispatch(new LoginWithPasswordCommand($email, $password));

                $acc = $this->accounts->get($userAccountId);
                $user = new UserAccountUser((int) $acc->id(), $acc->roles(), $acc->enabled());
                $this->security->login($user, null, 'main');

                return $this->redirectToRoute('person_list');
            } catch (\DomainException $e) {
                $error = $e->getMessage();
            }
        }

        return $this->render('auth/register.html.twig', [
            'error' => $error,
        ]);
    }

    #[Route('/login', name: 'login_page', methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        $error = null;

        if ($request->isMethod('POST')) {
            $email = $request->request->getString('email');
            $password = $request->request->getString('password');

            try {
                $userAccountId = (int) $this->commandBus->dispatch(new LoginWithPasswordCommand($email, $password));

                $acc = $this->accounts->get($userAccountId);
                $user = new UserAccountUser((int) $acc->id(), $acc->roles(), $acc->enabled());
                $this->security->login($user, null, 'main');

                return $this->redirectToRoute('person_list');
            } catch (\Throwable $e) {
                throw $e;
                $error = 'Niepoprawny email lub hasło.';
            }
        }

        return $this->render('auth/login.html.twig', [
            'error' => $error,
        ]);
    }
}
