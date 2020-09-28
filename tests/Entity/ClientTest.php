<?php

namespace App\Tests\Entity;

use App\Entity\Client;
use App\Tests\Utils\AssertHasErrors;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientTest extends KernelTestCase
{
    use AssertHasErrors;
    use FixturesTrait;

    /**
     * Create a valid entity for tests.
     */
    public function getEntity(): Client
    {
        $client = new Client();
        $client->setEmail('validclient@email.com')
            ->setCompany('Valid company')
            ->setPassword('password')
            ->setCreatedAt(new \DateTime())
            ->setRoles(['ROLE_USER']);

        return $client;
    }

    /**
     * Assert valid entity is valid.
     *
     * @return void
     */
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /**
     * Assert invalid entity (email, company) in invalid.
     *
     * @return void
     */
    public function testInvalidEntity()
    {
        $invalidClient = $this->getEntity();
        $invalidClient->setEmail('invalidclient.com')
            ->setCompany('')
            ->setPassword('pass');
        $this->assertHasErrors($invalidClient, 4);
    }

    /**
     * Assert client unicity with email.
     *
     * @return void
     */
    public function testInvalidUniqueEmail()
    {
        $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/clients.yaml',
        ]);
        $invalidClient = $this->getEntity()->setEmail('client1@email.com');
        $this->assertHasErrors($invalidClient, 1);
    }
}
