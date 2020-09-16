<?php

namespace App\Handler;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;

class PaginationHandler
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var CustomerRepository
     */
    private $customerReposiroty;

    public function __construct(SerializerInterface $serializer, ProductRepository $productRepository, ClientRepository $clientRepository, CustomerRepository $customerRepository)
    {
        $this->serializer = $serializer;
        $this->productRepository = $productRepository;
        $this->clientRepository = $clientRepository;
        $this->customerRepository = $customerRepository;
    }

    public function paginate(string $type, int $page, int  $limit, string $route, Client $user = null)
    {
        $repository = $type.'Repository';
        $paginatedRepresentation = $this->$repository->search(
            $page,
            $limit,
            $route,
            $user
        );
        $data = $this->serializer->serialize(
            $paginatedRepresentation, 
            'json', 
            SerializationContext::create()->setGroups(['Default', $type.'s_list'])
        );

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
}