<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class ProductController extends AbstractFOSRestController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Rest\Get(
     *      path = "/products/{id}",
     *      name = "app_products_show",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      serializerGroups={"product"}
     * )
     */
    public function showAction(Customer $customer)
    {
        return $customer;
    }

    /**
     * @Rest\Get(
     *      path = "/products",
     *      name = "app_products_list"
     * )
     * @Rest\View(
     *      serializerGroups={"product"}
     * )
     */
    public function listProducts()
    {
        $products = $this->productRepository->findAll();
        return $products;
    }
}