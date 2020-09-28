<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Customer;
use App\Exception\ResourceValidationException;
use App\Handler\ConstraintsViolationHandler;
use App\Handler\PaginationHandler;
use App\Repository\CustomerRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
