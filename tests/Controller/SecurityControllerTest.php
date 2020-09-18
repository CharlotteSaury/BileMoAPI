<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Constraints\Json;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * Test unauthorized access to unauthenticated users
     * 
     * @dataProvider provideAuthenticatedPages
     *
     * @param string $method
     * @param string $url
     * @return void
     */
    public function testRequiresAuthenticationPages($method, $url)
    {
        $client = static::createClient();
        $client->request($method, $url);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    public function provideAuthenticatedPages()
    {
        $entities = ['products', 'customers', 'clients'];
        $routes = [];
        foreach ($entities as $entity) {
            $routes[] = ['GET', '/api/'.$entity];
            $routes[] = ['GET', '/api/'.$entity.'/1'];
            $routes[] = ['DELETE', '/api/'.$entity.'/1'];
            $routes[] = ['POST', '/api/'.$entity];
            if ($entity == 'clients') {
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
                    'password' => 'admin'
                )
            )
        );
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $data);
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
            array(
                "username" => "admin@admin.com",
                "password" => "wrongpass"
            )
        );
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }
}