<?php

namespace App\Service;

use DateTime;
use App\Entity\Client;
use App\Entity\Customer;
use App\Handler\PaginationHandler;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Handler\ConstraintsViolationHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Exception\ResourceValidationException;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator, Security $security, PaginationHandler $paginationHandler, EntityManagerInterface $entityManager, CustomerRepository $customerRepository, ConstraintsViolationHandler $constraintsViolationHandler)
    {
        $this->security = $security;
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
        $this->paginationHandler = $paginationHandler;
        $this->constraintsViolationHandler = $constraintsViolationHandler;
        $this->validator = $validator;
    }

    /**
     * Handle customer list pagination
     *
     * @param ParamFetcherInterface $paramFetcher
     * @param Request $request
     * @param Client $client
     * @return Response
     */
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

    /**
     * Handle customer deletion by related client or admin
     *
     * @param Request $request
     * @param Client $client
     * @return void
     */
    public function handleDelete(Request $request, Client $client)
    {
        $customer = $this->customerRepository->findOneBy(['id' => $request->get('id')]);
        if ($customer) {
            if (!$this->security->isGranted('MANAGE', $customer)) {
                throw new AccessDeniedHttpException();
            }
            if ($customer->getClients()->contains($client)) {
                $customer->removeClient($client);
            } else {
                $this->entityManager->remove($customer);
            }
            $this->entityManager->flush();
        }
    }

    /**
     * Handle customer deletion
     *
     * @param Customer $customer
     * @param ConstraintViolationList $violations
     * @param Client $client
     * @return Customer $customer
     */
    public function handleCreate(Customer $customer, ConstraintViolationList $violations, Client $client)
    {
        $existingCustomer = $this->customerRepository->findOneBy(['email' => $customer->getEmail()]);
        if (!$existingCustomer) {
            $this->constraintsViolationHandler->validate($violations);
            $customer->addClient($client);
            $customer->setCreatedAt(new DateTime());
            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            return $customer;
        }
        if ($existingCustomer->getClients()->contains($client)) {
            throw new ResourceValidationException('This customer is already associated to this client');
        }
        $existingCustomer->addClient($client);
        $this->entityManager->flush();

        return $existingCustomer;
    }

    /**
     * Handle customer update
     *
     * @param Customer $customer
     * @param Request $request
     * @return Customer $customer
     */
    public function handleUpdate(Customer $customer, Request $request)
    {
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value) {
            if (in_array($key, Customer::ATTRIBUTES)) {
                $setter = 'set'.ucfirst($key);
                $customer->$setter($value);
            }
        }
        $errors = $this->validator->validate($customer);
        $this->constraintsViolationHandler->validate($errors);
        $this->entityManager->flush();

        return $customer;
    }
}
