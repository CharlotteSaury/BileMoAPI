<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientControllerTest extends WebTestCase
{

    /**
     * Test denied access to admin restricted routes related to client managment
     *
     * @dataProvider provideAdminRestrictedPages
     */
    public function testRestrictedRoutesClientNotAdmin($method, $url)
    {
        $client = static::createClient();
        $client->request($method, $url);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function provideAdminRestrictedPages()
    {
        return [
            ['GET', '/api/clients'],
            ['GET', '/api/clients/1'],
            ['POST', '/api/clients'],
            ['DELETE', '/api/clients'],
            ['PUT', '/api/clients/1']
        ];
    }

    public function testGetClientsNotAdmin()
    {

    }

    public function testGetClientAdmin()
    {

    }

    public function testGetClientsAdmin()
    {

    }

    public function testPostClientAdmin()
    {

    }

    public function testDeleteClientAdmin()
    {

    }
}