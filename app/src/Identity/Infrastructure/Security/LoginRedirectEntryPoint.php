<?php

namespace App\Identity\Infrastructure\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final class LoginRedirectEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(private UrlGeneratorInterface $urls) {}

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        // zachowaj target path automatycznie (Symfony to ogarnia przy redirectach)
        return new RedirectResponse($this->urls->generate('login_page'));
    }
}
