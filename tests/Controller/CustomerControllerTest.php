<?php

namespace App\Tests\Controller;

use App\Tests\Utils\CreateAuthenticatedClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerControllerTest extends WebTestCase
{
    use CreateAuthenticatedClient;

    /**
     * Test Error 405 when method not allowed
     *
     * @return void
     */
    public function testCustomersMethodNotAllowed()
    {
        $routes = [
            'DELETE' => '/api/customers',
            'PUT' => '/api/customers'
        ];
        $client = $this->createAuthenticatedClient();
        foreach ($routes as $method => $url) {
             $client->request($method, $url);
             $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        } 
    }

    /**
     * Test customers list request by authenticated client and presence of pagination information
     *
     * @return void
     */
    public function testGetCustomers()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/customers'
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $paginationInfos = ['page', 'limit', 'pages', '_links', '_embedded'];
        foreach ($paginationInfos as $info) {
            $this->assertArrayHasKey($info, $data);
        }
    }

    /**
     * Test related customer details request by authenticated client
     *
     * @return void
     */
    public function testGetRelatedCustomerDetails()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/customers/1'
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $customerAttributes = ['id', 'email', 'created_at', 'firstname', 'lastname', '_links'];
        foreach ($customerAttributes as $attribute) {
            $this->assertArrayHasKey($attribute, $data);
        }
    }

    /**
     * Test denied access for not related customer details request by authenticated client
     *
     * @return void
     */
    public function testGetNotRelatedCustomerDetails()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/customers/2'
        );
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Test error 404 return when requesting non existing resource
     *
     * @return void
     */
    public function testGetUnknownCustomerByAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'GET',
            '/api/customers/300'
        );
        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    

    /**
     * Handle post request for new customer
     *
     * @param Array $data
     * @return Object
     */
    public function postCustomer(Array $data) {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/customers',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($data)
        );
        
        return $client;
    }

    /**
     * Test new customer creation request with valid data
     *
     * @return void
     */
    public function testValidDataPostCustomer()
    {
        $data = [
            'email' => 'email@email.com',
            'firstname' => 'firstname',
            'lastname' => 'lastname'
        ];
        $client = $this->postCustomer($data);
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $customerAttributes = ['id', 'email', 'created_at', 'firstname', 'lastname', '_links'];
        foreach ($customerAttributes as $attribute) {
            $this->assertArrayHasKey($attribute, $data);
        }
    }

    /**
     * Test new customer request with invalid data
     *
     * @return void
     */
    public function testInvaliDataPostCustomer()
    {
        $data = [
            'email' => 'wrongemail',
            'firstname' => 'c'
        ];
        $client = $this->postCustomer($data);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    /**
     * Test new customer request with already associated customer
     *
     * @return void
     */
    public function testAlreadyAssociatedPostCustomer()
    {
        $data = [
            'email' => 'customer1@email.com'
        ];
        $client = $this->postCustomer($data);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    /**
     * Test already existing customer creation by another client request with valid data
     *
     * @return void
     */
    public function testValidDataPostExistingCustomer()
    {
        $data = [
            'email' => 'customer2@email.com',
            'firstname' => 'cust2',
            'lastname' => 'omer2'
        ];
        $client = $this->postCustomer($data);
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $customerAttributes = ['id', 'email', 'created_at', 'firstname', 'lastname', '_links'];
        foreach ($customerAttributes as $attribute) {
            $this->assertArrayHasKey($attribute, $data);
        }
    }

    /**
     * Test related customer deletion by authenticated client
     *
     * @return void
     */
    public function testDeleteRelatedCustomer()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'DELETE',
            '/api/customers/1'
        );
        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    /**
     * Test unrelated customer deletion by authenticated client
     *
     * @return void
     */
    public function testDeleteNotRelatedCustomer()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'DELETE',
            '/api/customers/2'
        );
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }


    /**
     * Test unknown customer deletion by authenticated client
     *
     * @return void
     */
    public function testDeleteUnknownCustomer()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'DELETE',
            '/api/customers/300'
        );
        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    /**
     * Handle related customer update request by authenticated client
     *
     * @param Array $data
     * @return Object
     */
    public function updateCustomer(int $customerId, string $email)
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'PUT',
            '/api/customers/'.$customerId,
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
     * Test related customer update request with valid data
     *
     * @return void
     */
    public function testGoodRequestUpdateRelatedCustomer()
    {
        $client = $this->updateCustomer(1, 'newcustomer1@email.com');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $customerAttributes = ['id', 'email', 'created_at', 'firstname', 'lastname', '_links'];
        foreach ($customerAttributes as $attribute) {
            $this->assertArrayHasKey($attribute, $data);
        }
        $this->assertSame($data['email'], 'newcustomer1@email.com');
    }

    /**
     * Test related customer update request with invalid data
     *
     * @return void
     */
    public function testBadRequestUpdateRelatedCustomer()
    {
        $client = $this->updateCustomer(1, 'customer2@email.com');
        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    /**
     * Test unrelated customer update request with valid data
     *
     * @return void
     */
    public function testUpdateNotRelatedCustomer()
    {
        $client = $this->updateCustomer(2, 'newcustomer2@email.com');
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    
}