<?php

namespace App\Service;

use DateTime;
use App\Entity\Product;
use App\Handler\PaginationHandler;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Handler\ConstraintsViolationHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class ProductService
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
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(PaginationHandler $paginationHandler, EntityManagerInterface $entityManager, ProductRepository $productRepository, ConstraintsViolationHandler $constraintsViolationHandler)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
        $this->paginationHandler = $paginationHandler;
        $this->constraintsViolationHandler = $constraintsViolationHandler;
    }

    /**
     * Handle product list pagination
     *
     * @param ParamFetcherInterface $paramFetcher
     * @param Request $request
     * @return Response
     */
    public function handleList(ParamFetcherInterface $paramFetcher, Request $request)
    {
        $paginatedRepresentation = $this->paginationHandler->paginate(
            'product',
            $paramFetcher->get('page'),
            $paramFetcher->get('limit'),
            $request->get('_route')
        );

        return $paginatedRepresentation;
    }

    /**
     * Handle product deletion
     *
     * @param Request $request
     * @return void
     */
    public function handleDelete(Request $request)
    {
        $product = $this->productRepository->findOneBy(['id' => $request->get('id')]);
        if ($product) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();
        }
    }

    /**
     * Handle product creation 
     *
     * @param Product $product
     * @param ConstraintViolationList $violations
     * @return Product $product
     */
    public function handleCreate(Product $product, ConstraintViolationList $violations)
    {
        $this->constraintsViolationHandler->validate($violations);

        $product->setCreatedAt(new DateTime());
        if (null != $product->getConfigurations()) {
            foreach ($product->getConfigurations() as $config) {
                $config->setProduct($product);
                foreach ($config->getImages() as $image) {
                    $image->setConfiguration($config);
                }
            }
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }
}
