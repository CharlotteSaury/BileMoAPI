<?php

namespace App\Tests\Controller;

use App\Tests\Utils\CreateAuthenticatedClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientControllerTest extends WebTestCase
{
    use CreateAuthenticatedClient;

    /**
     * Test denied access to admin restricted routes related to client managment
     *
     * @dataProvider provideAdminRestrictedPages
     * 
     */
    public function testRestrictedRoutesClientNotAdmin($method, $url)
    {
        $client = $this->createAuthenticatedClient();
        $client->request($method, $url);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function provideAdminRestrictedPages()
    {
        return [
            ['GET', '/api/clients'],
            ['GET', '/api/clients/2'],
            ['POST', '/api/clients'],
            ['DELETE', '/api/clients'],
            ['PUT', '/api/clients/2']
        ];
    }

    public function testGetClientsNotAdmin()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/clients'
        );
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testGetClientAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'GET',
            '/api/clients/1'
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testGetUnknownClientAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'GET',
            '/api/clients/300'
        );
        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testGetClientsAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'GET',
            '/api/clients'
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testPostClientAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'POST',
            '/api/clients',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'email' => 'email@email.com',
                'company' => 'companyTest',
                'password' => 'temporarypass'
            ))
        );
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $clientAttributes = ['id', 'email', 'created_at', 'company', 'customers', '_links'];
        foreach ($clientAttributes as $attribute) {
            $this->assertArrayHasKey($attribute, $data);
        }
    }

    public function testBadRequestPostClientAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'POST',
            '/api/clients',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'email' => '',
                'company' => 'co'
            ))
        );
        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testDeleteClientAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'DELETE',
            '/api/clients/1'
        );
        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    public function testDeleteUnknownClientAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'DELETE',
            '/api/clients/300'
        );
        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }
}