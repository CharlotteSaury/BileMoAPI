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
     */
    public function testRestrictedRoutesClientByNotAdmin()
    {
        $routes = [
            'GET' => '/api/clients',
            'GET' => '/api/clients/2',
            'DELETE' => '/api/clients/1'
        ];
        $client = $this->createAuthenticatedClient();
        foreach ($routes as $method => $url) {
             $client->request($method, $url);
             $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
        } 
    }

    /**
     * Test Error 405 when method not allowed
     *
     * @return void
     */
    public function testClientsMethodNotAllowed()
    {
        $routes = [
            'DELETE' => '/api/clients',
            'PUT' => '/api/clients'
        ];
        $client = $this->createAuthenticatedClient();
        foreach ($routes as $method => $url) {
             $client->request($method, $url);
             $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } 
    }

    /**
     * Test Client details request by authenticated admin
     *
     * @return void
     */
    public function testGetClientByAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'GET',
            '/api/clients/1'
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $clientAttributes = ['id', 'email', 'created_at', 'company', 'customers', '_links'];
        foreach ($clientAttributes as $attribute) {
            $this->assertArrayHasKey($attribute, $data);
        }
    }

    /**
     * Test error 404 return when requesting non existing resource
     *
     * @return void
     */
    public function testGetUnknownClientByAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'GET',
            '/api/clients/300'
        );
        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    /**
     * Test client list request by authenticated admin and presence of pagination information
     *
     * @return void
     */
    public function testGetClientsByAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'GET',
            '/api/clients'
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $paginationInfos = ['page', 'limit', 'pages', '_links', '_embedded'];
        foreach ($paginationInfos as $info) {
            $this->assertArrayHasKey($info, $data);
        }
    }

    /**
     * Handle post request for new client
     *
     * @param Array $data
     * @return Object
     */
    public function postClientByAdmin(Array $data) {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'POST',
            '/api/clients',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($data)
        );
        return $client;
    }

    /**
     * Test new client creation request with valid data
     *
     * @return void
     */
    public function testPostClientByAdmin()
    {
        $data = [
            'email' => 'email@email.com',
            'company' => 'companyTest',
            'password' => 'temporarypass'
        ];
        $client = $this->postClientByAdmin($data);
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $clientAttributes = ['id', 'email', 'created_at', 'company', 'customers', '_links'];
        foreach ($clientAttributes as $attribute) {
            $this->assertArrayHasKey($attribute, $data);
        }
    }

    /**
     * Test new client request with invalid data
     *
     * @return void
     */
    public function testBadRequestPostClientByAdmin()
    {
        $data = [
            'email' => '',
            'company' => 'co'
        ];
        $client = $this->postClientByAdmin($data);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    /**
     * Test existing client deletion by authenticated admin
     *
     * @return void
     */
    public function testDeleteClientByAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'DELETE',
            '/api/clients/1'
        );
        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    /**
     * Test unknown client deletion by authenticated admin
     *
     * @return void
     */
    public function testDeleteUnknownClientByAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'DELETE',
            '/api/clients/300'
        );
        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    /**
     * Handle client update request by authenticated admin
     *
     * @param Array $data
     * @return Object
     */
    public function updateClientByAdmin(string $email)
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'PUT',
            '/api/clients/1',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'email' => $email
            ))
        );
        return $client;
    }
    
    /**
     * Test client update request with valid data
     *
     * @return void
     */
    public function testGoodRequestUpdateClientByAdmin()
    {
        $client = $this->updateClientByAdmin('newclient1@email.com');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $clientAttributes = ['id', 'email', 'created_at', 'company', 'customers', '_links'];
        foreach ($clientAttributes as $attribute) {
            $this->assertArrayHasKey($attribute, $data);
        }
        $this->assertSame($data['email'], 'newclient1@email.com');
    }

    /**
     * Test client update request with invalid data
     *
     * @return void
     */
    public function testBadRequestUpdateClientByAdmin()
    {
        $client = $this->updateClientByAdmin('admin@bilemo.com');
        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    
}