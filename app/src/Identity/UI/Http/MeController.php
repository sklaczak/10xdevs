<?php

namespace App\Identity\UI\Http;

use App\Identity\Application\Query\GetCurrentUser\GetCurrentUserQuery;
use App\Shared\Application\Bus\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class MeController extends AbstractController
{
    #[Route('/me', name: 'auth_me', methods: ['GET'])]
    public function me(QueryBus $queryBus): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['ok' => false, 'error' => 'Brak sesji.'], 401);
        }

        $userAccountId = (int) $user->getUserIdentifier();

        $dto = $queryBus->ask(new GetCurrentUserQuery($userAccountId));

        return new JsonResponse([
            'ok' => true,
            'userAccountId' => $dto->userAccountId,
            'personId' => $dto->personId,
            'roles' => $dto->roles,
            'enabled' => $dto->enabled,
        ]);
    }
}
