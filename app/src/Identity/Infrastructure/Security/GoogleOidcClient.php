<?php

namespace App\Identity\Infrastructure\Security;

use Google\Client;

final class GoogleOidcClient
{
    private Client $client;

    public function __construct(private string $googleClientId)
    {
        $this->client = new Client(['client_id' => $this->googleClientId]);
    }

    /**
     * @return array<string, mixed>
     */
    public function verifyIdToken(string $idToken): array
    {
        $payload = $this->client->verifyIdToken($idToken);

        if ($payload === false) {
            throw new \RuntimeException('Niepoprawny lub nieważny Google ID token.');
        }

        return $payload;
    }
}
