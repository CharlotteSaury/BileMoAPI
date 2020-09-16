<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProductController extends AbstractFOSRestController
{
    /**
     * @var ProductService
     */
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
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
     * 
     * @Cache(maxage="3600", public=true, mustRevalidate=true)
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
     *      serializerGroups={"products_list"}
     * )
     * 
     * @Cache(maxage="15", public=true, mustRevalidate=true)
     * 
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="^[1-9]+[0-9]*$",
     *     default="1",
     *     description="Current page of product list."
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="^[1-9]+[0-9]*$",
     *     default="10",
     *     description="Maximum number of products per page."
     * )
     */
    public function listAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        return $this->productService->handleList($paramFetcher, $request);
    }

    /**
     * @Rest\Delete(
     *      path = "/api/products/{id}",
     *      name = "app_products_delete",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      StatusCode = Response::HTTP_NO_CONTENT
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction(Request $request)
    {
        $this->productService->handleDelete($request);
    }

    /**
     * @Rest\Post(
     *      path = "/api/products",
     *      name = "app_products_create"
     * )
     * @Rest\View(
     *      StatusCode = 201,
     *      serializerGroups={"product"}
     * )
     * @ParamConverter(
     *      "product", 
     *      converter="fos_rest.request_body"
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function createAction(Product $product, ConstraintViolationList $violations)
    {
        $newProduct = $this->productService->handleCreate($product, $violations);
        
        return $this->view(
            $newProduct,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_products_show', ['id' => $product->getId()], UrlGeneratorInterface::ABSOLUTE_URL)]
        );
    }
}
