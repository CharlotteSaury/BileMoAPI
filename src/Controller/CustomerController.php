<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Service\CustomerService;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @Rest\Get(
     *      path = "/api/customers/{id}",
     *      name = "app_customers_show",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      serializerGroups={"customer"}
     * )
     *
     * @Cache(maxage="3600", public=true, mustRevalidate=true)
     * 
     * @IsGranted("MANAGE", subject="customer")
     */
    public function showAction(Customer $customer)
    {
        return $customer;
    }

    /**
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
     * @Cache(maxage="3600", public=true, mustRevalidate=true)
     */
    public function listAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        return $this->customerService->handleList($paramFetcher, $request, $this->getUser());;
    }

    /**
     * @Rest\Post(
     *      path = "/api/customers",
     *      name = "app_customers_create"
     * )
     * @Rest\View(
     *      StatusCode = 201,
     *      serializerGroups={"customer"}
     * )
     * @ParamConverter("customer", converter="fos_rest.request_body")
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
     * @Rest\Delete(
     *      path = "/api/customers/{id}",
     *      name = "app_customers_delete",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      StatusCode = Response::HTTP_NO_CONTENT
     * )
     */
    public function deleteAction(Request $request)
    {
        $this->customerService->handleDelete($request);
    }
}
