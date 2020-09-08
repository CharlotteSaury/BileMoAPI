<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class CustomerController extends AbstractFOSRestController
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @Rest\Get(
     *      path = "/customers/{id}",
     *      name = "app_customers_show",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      serializerGroups={"customer"}
     * )
     */
    public function showAction(Customer $customer)
    {
        return $customer;
    }

    /**
     * @Rest\Get(
     *      path = "/customers",
     *      name = "app_customers_list"
     * )
     * @Rest\View(
     *      serializerGroups={"customer"}
     * )
     */
    public function listCustomers()
    {
        $customers = $this->customerRepository->findAll();
        return $customers;
    }
}