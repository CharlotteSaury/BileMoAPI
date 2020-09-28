<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

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
     * List the characteristics of the specified product.
     *
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
     *
     * @SWG\Get(
     *     description="List the characteristics of the specified product",
     *     tags = {"Product"},
     *     @SWG\Response(
     *          response=200,
     *          @Model(type=Product::class),
     *          description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request: This method is not allowed for this route",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: Expired JWT Token/JWT Token not found",
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Object not found: Invalid route or resource ID",
     *     ),
     *     @SWG\Parameter(
     *          name="id",
     *          required= true,
     *          in="path",
     *          type="integer",
     *          description="The product unique identifier.",
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required= true,
     *          in="header",
     *          type="string",
     *          description="Bearer JWT Token",
     *     )
     * )
     * 
     * @return Response
     */
    public function showAction(Product $product)
    {
        return $product;
    }

    /**
     * List all the available BileMo products (smartphones).
     *
     * @Rest\Get(
     *      path = "/api/products",
     *      name = "app_products_list"
     * )
     * @Rest\View(
     *      serializerGroups={"products_list"}
     * )
     *
     * @Cache(maxage="3600", public=true, mustRevalidate=true)
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
     *
     * @SWG\Get(
     *     description="List all the available BileMo products (smartphones)",
     *     tags = {"Product"},
     *     @SWG\Response(
     *          response=200,
     *          @Model(type=Product::class),
     *          description="Successful operation: Returns a list of all products",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request: This method is not allowed for this route",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: Expired JWT Token/JWT Token not found",
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Object not found: Invalid route or resource ID",
     *     ),
     *     @SWG\Parameter(
     *          name="page",
     *          required= false,
     *          in="query",
     *          type="integer",
     *          description="Page number in the product list",
     *     ),
     *      @SWG\Parameter(
     *          name="limit",
     *          required= false,
     *          in="query",
     *          type="integer",
     *          description="Number of items per page",
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required= true,
     *          in="header",
     *          type="string",
     *          description="Bearer Token",
     *     )
     * )
     * 
     * @return Response
     */
    public function listAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        return $this->productService->handleList($paramFetcher, $request);
    }

    /**
     * Delete the specified product.
     *
     * @Rest\Delete(
     *      path = "/api/products/{id}",
     *      name = "app_products_delete",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      StatusCode = Response::HTTP_NO_CONTENT
     * )
     * @IsGranted("ROLE_ADMIN")
     *
     * @SWG\Delete(
     *     description="Delete the specified product",
     *     tags = {"Product"},
     *     @SWG\Response(
     *          response=204,
     *          description="Successful operation: No-Content",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request: This method is not allowed for this route",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: Expired JWT Token/JWT Token not found",
     *     ),
     *     @SWG\Response(
     *          response="403",
     *          description="Forbidden: You are not allowed to access to this page"),
     *     @SWG\Response(
     *         response="404",
     *         description="Object not found: Invalid route or resource ID",
     *     ),
     *     @SWG\Parameter(
     *          name="id",
     *          required= true,
     *          in="path",
     *          type="integer",
     *          description="The product unique identifier.",
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required= true,
     *          in="header",
     *          type="string",
     *          description="Bearer JWT Token",
     *     )
     * )
     * 
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        $this->productService->handleDelete($request);
    }

    /**
     * Create a new product.
     *
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
     *
     * @SWG\Post(
     *     description="Create a new product",
     *     tags = {"Product"},
     *     @SWG\Response(
     *          response=201,
     *          description="Successful operation: Created",
     *          @Model(type=Product::class)
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Bad Request: This method is not allowed for this route OR Could not decode JSON, syntax error - malformed JSON. OR The JSON sent contains invalid data. Here are the errors you need to correct: XX",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized: Expired JWT Token/JWT Token not found",
     *     ),
     *     @SWG\Response(
     *          response="403",
     *          description="Forbidden: You are not allowed to access to this page"),
     *     @SWG\Response(
     *         response="404",
     *         description="Object not found: Invalid route or resource ID",
     *     ),
     *     @SWG\Parameter(
     *          name="Body",
     *          required= true,
     *          in="body",
     *          type="string",
     *          description="All product characteristics",
     *          @SWG\Schema(
     *              type="array",
     *              example= {
     *                  "name": "phone name",
     *                  "description": "description",
     *                  "manufacturer": "Wiko",
     *                  "screen": 5.9,
     *                  "das": 0.328,
     *                  "weight": 200.7,
     *                  "length": 12.58,
     *                  "width": 8.11,
     *                  "height": 1.02,
     *                  "wifi": true,
     *                  "video4k": false,
     *                  "bluetooth": false,
     *                  "lte4_g": false,
     *                  "camera": false,
     *                  "nfc": true,
     *                  "configurations": {
     *                      {
     *                          "memory": 32,
     *                          "color": "lime",
     *                          "price": 1413.39,
     *                          "images": {
     *                              {
     *                                  "url": "https://duval.com/a-amet-laboriosam-totam-iusto.html"
     *                              },
     *                              {
     *                                  "url": "https://duval.com/a-amet-riosam-totam-iusto.html"
     *                              }
     *                          }
     *                      }
     *                  }
     *              },
     *              @Model(type=Product::class)
     *          )
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required= true,
     *          in="header",
     *          type="string",
     *          description="Bearer JWT Token",
     *     )
     * )
     * 
     * @return Response
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
