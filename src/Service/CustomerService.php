<?php

namespace App\Service;

use DateTime;
use App\Entity\Client;
use App\Entity\Product;
use App\Entity\Customer;
use App\Handler\PaginationHandler;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Handler\ConstraintsViolationHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class CustomerService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PaginationHandler
     */
    private $paginationHandler;

    /**
     * @var ConstraintsViolationHandler 
     */
    private $constraintsViolationHandler;

    /**
     * @var CustomerRepository 
     */
    private $customerRepository;

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security, PaginationHandler $paginationHandler, EntityManagerInterface $entityManager, CustomerRepository $customerRepository, ConstraintsViolationHandler $constraintsViolationHandler)
    {
        $this->security = $security;
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
        $this->paginationHandler = $paginationHandler;
        $this->constraintsViolationHandler = $constraintsViolationHandler;
    }

    public function handleList(ParamFetcherInterface $paramFetcher, Request $request, Client $client)
    {
        $paginatedRepresentation = $this->paginationHandler->paginate(
            'customer', 
            $paramFetcher->get('page'), 
            $paramFetcher->get('limit'), 
            $request->get('_route'),
            $client
        );
        return $paginatedRepresentation;
    }

    public function handleDelete(Request $request)
    {
        $customer = $this->customerRepository->findOneBy(['id' => $request->get('id')]);
        if ($customer) {
            if (!$this->security->isGranted('MANAGE', $customer)) {
                throw new AccessDeniedHttpException();
            }
            $this->entityManager->remove($customer);
            $this->entityManager->flush();
        }
    }

    public function handleCreate(Customer $customer, ConstraintViolationList $violations, Client $client)
    {
        $this->constraintsViolationHandler->validate($violations);

        $customers = $this->customerRepository->findBy(['client' => $client]);
        foreach ($customers as $currentCustomer) {
            if ($currentCustomer->getEmail() === $customer->getEmail()) {
                return new JsonResponse([
                    'code' => 400,
                    'message' => 'This customer is already associated to this client'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        $customer->setClient($client);
        $customer->setCreatedAt(new DateTime());
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return $customer;
    }
}
