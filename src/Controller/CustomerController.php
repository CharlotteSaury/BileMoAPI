<?php

namespace App\Controller;

use App\Entity\Customer;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class CustomerController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(
     *      path = "/customers/{id}",
     *      name = "app_customers_show",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      serializerGroups={"show_customer"}
     * )
     */
    public function showAction(Customer $customer)
    {
        return $customer;
    }
}