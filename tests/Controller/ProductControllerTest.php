<?php

namespace App\Tests\Controller;

use App\Tests\Utils\CreateAuthenticatedClient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    use CreateAuthenticatedClient;

    /**
     * Test Error 405 when method not allowed.
     *
     * @return void
     */
    public function testProductsMethodNotAllowed()
    {
        $routes = [
            'DELETE' => '/api/products',
            'PUT' => '/api/products',
        ];
        $client = $this->createAuthenticatedClient();
        foreach ($routes as $method => $url) {
            $client->request($method, $url);
            $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
        }
    }

    /**
     * Test products list request by authenticated client and presence of pagination information.
     *
     * @return void
     */
    public function testGetProducts()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/products'
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $paginationInfos = ['page', 'limit', 'pages', '_links', '_embedded'];
        foreach ($paginationInfos as $info) {
            $this->assertArrayHasKey($info, $data);
        }
    }

    /**
     * Test related product details request by authenticated client.
     *
     * @return void
     */
    public function testGetRelatedProductDetails()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/products/1'
        );
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $productAttributes = ['id', 'name', 'created_at', 'description', 'screen', 'das', 'weight', 'length', 'width', 'height', 'wifi', 'video4k', 'bluetooth', 'camera', 'manufacturer', '_links'];
        foreach ($productAttributes as $attribute) {
            $this->assertArrayHasKey($attribute, $data);
        }
    }

    /**
     * Test error 404 return when requesting non existing resource.
     *
     * @return void
     */
    public function testGetUnknownProduct()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'GET',
            '/api/products/300'
        );
        $this->assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    /**
     * Handle post request for new product.
     *
     * @return object
     */
    public function postProduct(array $data)
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'POST',
            '/api/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        return $client;
    }

    /**
     * Test new product creation request with valid data.
     *
     * @return void
     */
    public function testValidDataPostProduct()
    {
        $data = [
            'name' => 'quas',
            'description' => 'Illum deleniti et iure ut. Eos est tempora aperiam labore enim quo. Quasi ducimus aut et omnis cupiditate voluptatem praesentium.',
            'manufacturer' => 'Huawei',
            'screen' => 4.4,
            'das' => 0.837,
            'weight' => 186.1,
            'length' => 14.76,
            'width' => 6.57,
            'height' => 0.76,
            'wifi' => true,
            'video4k' => true,
            'bluetooth' => false,
            'camera' => false,
            'configurations' => [
                [
                'memory' => 64,
                'color' => 'lime',
                'price' => 1424.48,
                'images' => [
                        [
                            'url' => 'http://www.rodrigues.net/occaecati-ipsum-molestiae-natus-rerum-rem-necessitatibus',
                        ],
                    ],
                ],
            ],
        ];
        $client = $this->postProduct($data);
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $productAttributes = ['id', 'name', 'created_at', 'description', 'screen', 'das', 'weight', 'length', 'width', 'height', 'wifi', 'video4k', 'bluetooth', 'camera', 'manufacturer', '_links'];
        foreach ($productAttributes as $attribute) {
            $this->assertArrayHasKey($attribute, $data);
        }
    }

    /**
     * Test new product request with invalid data.
     *
     * @return void
     */
    public function testInvaliDataPostProduct()
    {
        $data = [
            'name' => 'phonezefze',
            'description' => 'I',
            'manufacturer' => '',
            'screen' => 4.4,
            'das' => 0.837,
            'weight' => 186.1,
            'length' => 14.76,
            'configurations' => [],
        ];
        $client = $this->postProduct($data);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    /**
     * Test new product request with already existing product.
     *
     * @return void
     */
    public function testAlreadyExistingPostProduct()
    {
        $data = [
            'name' => 'phone1',
            'description' => 'Illum deleniti et iure ut. Eos est tempora aperiam labore enim quo. Quasi ducimus aut et omnis cupiditate voluptatem praesentium.',
            'manufacturer' => 'Huawei',
            'screen' => 4.4,
            'das' => 0.837,
            'weight' => 186.1,
            'length' => 14.76,
            'width' => 6.57,
            'height' => 0.76,
            'wifi' => true,
            'video4k' => true,
            'bluetooth' => false,
            'camera' => false,
            'configurations' => [
                [
                'memory' => 64,
                'color' => 'lime',
                'price' => 1424.48,
                'images' => [
                        [
                            'url' => 'http://www.rodrigues.net/occaecati-ipsum-molestiae-natus-rerum-rem-necessitatibus',
                        ],
                    ],
                ],
            ],
        ];
        $client = $this->postProduct($data);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    /**
     * Test product deletion by authenticated client not granted admin.
     *
     * @return void
     */
    public function testDeleteProductByNonAdmin()
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'DELETE',
            '/api/products/1'
        );
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * Test product deletion by admin.
     *
     * @return void
     */
    public function testDeleteProductByAdmin()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'DELETE',
            '/api/products/1'
        );
        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }

    /**
     * Test unknown product deletion by admin.
     *
     * @return void
     */
    public function testDeleteUnknownProduct()
    {
        $client = $this->createAuthenticatedClient('admin@bilemo.com', 'password');
        $client->request(
            'DELETE',
            '/api/products/300'
        );
        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }
}
