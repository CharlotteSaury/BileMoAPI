<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Service\CustomerService;
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
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

class CustomerController extends AbstractFOSRestController
{
    /**
     * @var CustomerService
     */
    private $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * List characteristics of specified customer
     * 
     * @Rest\Get(
     *      path = "/api/customers/{id}",
     *      name = "app_customers_show",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      serializerGroups={"customer"}
     * )
     *
     * @Cache(maxage="3600", public=private, mustRevalidate=true)
     * 
     * @IsGranted("MANAGE", subject="customer")
     * 
     * @SWG\Get(
     *     description="List the characteristics of the specified customer (Restricted to admin and related client)",
     *     tags = {"Customer"},
     *     @SWG\Response(
     *          response=200,
     *          @Model(type=Customer::class),
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
     *          description="The customer unique identifier.",
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required= true,
     *          in="header",
     *          type="string",
     *          description="Bearer JWT Token",
     *     )
     * )
     */
    public function showAction(Customer $customer)
    {
        return $customer;
    }

    /**
     * List customers associated to authenticated client
     * 
     * @Rest\Get(
     *      path = "/api/customers",
     *      name = "app_customers_list"
     * )
     * @Rest\View()
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
     * @Cache(maxage="3600", public=private, mustRevalidate=true)
     * 
     * @SWG\Get(
     *     description="List customers associated to authenticated client",
     *     tags = {"Customer"},
     *     @SWG\Response(
     *          response=200,
     *          @Model(type=Customer::class),
     *          description="Successful operation: Returns a list of customers related to authenticated client",
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
     *          description="Page number in the customer list",
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
     */
    public function listAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        return $this->customerService->handleList($paramFetcher, $request, $this->getUser());;
    }

    /**
     * Allow an authenticated client to create a new customer
     * 
     * @Rest\Post(
     *      path = "/api/customers",
     *      name = "app_customers_create"
     * )
     * @Rest\View(
     *      StatusCode = 201,
     *      serializerGroups={"customer"}
     * )
     * @ParamConverter("customer", converter="fos_rest.request_body")
     * 
     * @SWG\Post(
     *     description="Allow an authenticated client to create a new customer",
     *     tags = {"Customer"},
     *     @SWG\Response(
     *          response=201,
     *          description="Successful operation: Created",
     *          @Model(type=Customer::class)
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
     *          description="All customer characteristics",
     *          @SWG\Schema(
     *              type="array",
     *              example={"email": "customer@email.com", "first_name": "Martin", "last_name": "Dupond"},
     *              @Model(type=Customer::class)
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
     */
    public function createAction(Customer $customer, ConstraintViolationList $violations)
    {
        $newCustomer = $this->customerService->handleCreate($customer, $violations, $this->getUSer());
        
        return $this->view(
            $newCustomer,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_customers_show', ['id' => $newCustomer->getId()], UrlGeneratorInterface::ABSOLUTE_URL)]
        );
    }

    /**
     * Allow an authenticated client to delete a customer
     * 
     * @Rest\Delete(
     *      path = "/api/customers/{id}",
     *      name = "app_customers_delete",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      StatusCode = Response::HTTP_NO_CONTENT
     * )
     * 
     * @SWG\Delete(
     *     description="Allow an authenticated client to delete a customer",
     *     tags = {"Customer"},
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
     *          description="The customer unique identifier.",
     *     ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          required= true,
     *          in="header",
     *          type="string",
     *          description="Bearer JWT Token",
     *     )
     * )
     */
    public function deleteAction(Request $request)
    {
        $this->customerService->handleDelete($request);
    }
}
