<?php

namespace App\Tests\Utils;

use Liip\TestFixturesBundle\Test\FixturesTrait;

trait createAuthenticatedClient
{
    use FixturesTrait;

    /**
     * Create an authenticated client.
     **/
    public function createAuthenticatedClient($username = 'client1@email.com', $password = 'password')
    {
        $client = static::createClient();
        $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/clients.yaml',
            dirname(__DIR__).'/fixtures/customers.yaml',
            dirname(__DIR__).'/fixtures/products.yaml',
        ]);
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $username,
                'password' => $password,
            ])
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        self::ensureKernelShutdown();

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', 'Bearer '.$data['token']);

        return $client;
    }
}
