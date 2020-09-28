<?php

namespace App\Tests\Handler;

use App\Handler\PaginationHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class PaginationHandlerTest extends TestCase
{
    public function testPaginationReturningResponse()
    {
        $serializer = $this->getMockBuilder('JMS\Serializer\SerializerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $productRepository = $this->getMockBuilder('App\Repository\ProductRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $clientRepository = $this->getMockBuilder('App\Repository\ClientRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $customerRepository = $this->getMockBuilder('App\Repository\CustomerRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $paginationHandler = new PaginationHandler($serializer, $productRepository, $clientRepository, $customerRepository);

        $response = $paginationHandler->paginate('product', 1, 15, 'app_products_list');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $headers = $response->headers->all();
        $this->assertArrayHasKey('content-type', $headers);
        $this->assertSame('application/json', $headers['content-type'][0]);
    }
}
