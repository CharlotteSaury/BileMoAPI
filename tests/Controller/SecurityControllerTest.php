<?php

namespace App\Tests\Controller;

use App\Tests\Utils\createAuthenticatedClient;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;
    use createAuthenticatedClient;

    /**
     * Test unauthorized access to unauthenticated users
     * 
     * @dataProvider provideAuthenticatedRequests
     *
     * @param string $method
     * @param string $url
     * @return void
     */
    public function testRequiresAuthenticationRequests($method, $url)
    {
        $client = static::createClient();
        $client->request($method, $url);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    public function provideAuthenticatedRequests()
    {
        $entities = ['products', 'customers', 'clients'];
        $routes = [];
        foreach ($entities as $entity) {
            $routes[] = ['GET', '/api/'.$entity];
            $routes[] = ['GET', '/api/'.$entity.'/1'];
            $routes[] = ['DELETE', '/api/'.$entity.'/1'];
            $routes[] = ['POST', '/api/'.$entity];
            if (in_array($entity, ['clients', 'customers'])) {
                $routes[] = ['PUT', '/api/'.$entity.'/1'];
            }
        }

        return $routes;
    }

    /**
     * Test authentication token obtention with valid credentials
     *
     * @return void
     */
    public function testPOSTCreateToken()
    {
        $client = static::createClient();
        $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/clients.yaml'
        ]);
        $client->request(
            'POST',
            '/api/login_check',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'username' => 'admin@bilemo.com',
                'password' => 'password'
            ))
        );
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
        $token = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $token);
    }

    /**
     * Test authentication token obtention with invalid credentials
     *
     * @return void
     */
    public function testPOSTTokenInvalidCredentials()
    {
        $client = static::createClient();
        $this->loadFixtureFiles([
            dirname(__DIR__).'/fixtures/clients.yaml'
        ]);
        $client->request(
            'POST',
            '/api/login_check',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'username' => 'admin@bilemo.com',
                'password' => 'badpassword'
            ))
        );
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }
}