<?php

namespace App\Controller;

use App\Entity\Product;
use App\Handler\AuthorizationJsonHandler;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class ProductController extends AbstractFOSRestController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AuthorizationJsonHandler
     */
    private $authorizationHandler;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager, AuthorizationJsonHandler $authorizationHandler)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
        $this->authorizationHandler = $authorizationHandler;
    }

    /**
     * @Rest\Get(
     *      path = "/api/products/{id}",
     *      name = "app_products_show",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      serializerGroups={"product"}
     * )
     */
    public function showAction(Product $product)
    {
        return $product;
    }

    /**
     * @Rest\Get(
     *      path = "/api/products",
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

    /**
     * @Rest\Delete(
     *      path = "/api/products/{id}",
     *      name = "app_products_delete",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      StatusCode = 204
     * )
     */
    public function deleteAction(Product $product) 
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->authorizationHandler->forbiddenResponse('delete', 'product');
        }
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}