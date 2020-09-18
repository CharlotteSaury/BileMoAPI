<?php

namespace App\Controller;

use App\Entity\Client;
use App\Service\ClientService;
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

class ClientController extends AbstractFOSRestController
{
    /**
     * @var ClientService
     */
    private $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * List characteristic of the specified BileMo's client
     * 
     * @Rest\Get(
     *      path = "/api/clients/{id}",
     *      name = "app_clients_show",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      serializerGroups={"client"}
     * )
     * 
     * @Cache(maxage="3600", public=false, mustRevalidate=true)
     * 
     * @IsGranted("MANAGE", subject="client")
     * 
     * @SWG\Get(
     *     description="List the characteristics of the specified client (Restricted to admin)",
     *     tags = {"Client"},
     *     @SWG\Response(
     *          response=200,
     *          @Model(type=Client::class),
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
     *          description="The client unique identifier.",
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
    public function showAction(Client $client)
    {
        return $client;
    }

    /**
     * List all BileMo's clients
     * 
     * @Rest\Get(
     *      path = "/api/clients",
     *      name = "app_clients_list"
     * )
     * @Rest\View()
     * 
     * @Cache(maxage="3600", public=false, mustRevalidate=true)
     * 
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="^[1-9]+[0-9]*$",
     *     default="1",
     *     description="Current page of client list. "
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="^[1-9]+[0-9]*$",
     *     default="10",
     *     description="Maximum number of clients per page."
     * )
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @SWG\Get(
     *     description="List all BileMo's clients (Restricted to admin)",
     *     tags = {"Client"},
     *     @SWG\Response(
     *          response=200,
     *          @Model(type=Client::class),
     *          description="Successful operation: Returns a list of all clients",
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
     *          description="Page number in the client list",
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
        return $this->clientService->handleList($paramFetcher, $request);
    }

    /**
     * Create a new BileMo's client
     * 
     * @Rest\Post(
     *      path = "/api/clients",
     *      name = "app_clients_create"
     * )
     * @Rest\View(
     *      StatusCode = 201,
     *      serializerGroups={"client"}
     * )
     * @ParamConverter("client", converter="fos_rest.request_body")
     * @IsGranted("ROLE_ADMIN")
     * 
     * @SWG\Post(
     *     description="Create a new client (Restricted to admin)",
     *     tags = {"Client"},
     *     @SWG\Response(
     *          response=201,
     *          description="Successful operation: Created",
     *          @Model(type=Client::class)
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
     *          description="All client characteristics",
     *          @SWG\Schema(
     *              type="array",
     *              example={"email": "client@company.com", "company": "Company SARL", "password": "TemporaryPass"},
     *              @Model(type=Client::class)
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
    public function createAction(Client $client, ConstraintViolationList $violations)
    {
        $newClient = $this->clientService->handleCreate($client, $violations);
        
        return $this->view(
            $newClient,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_clients_show', ['id' => $newClient->getId()], UrlGeneratorInterface::ABSOLUTE_URL)]
        );
    }

    /**
     * Delete a BileMo's client
     * 
     * @Rest\Delete(
     *      path = "/api/clients/{id}",
     *      name = "app_clients_delete",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      StatusCode = Response::HTTP_NO_CONTENT
     * )
     * @IsGranted("ROLE_ADMIN")
     * 
     * @SWG\Delete(
     *     description="Delete the specified client (Restricted to admin)",
     *     tags = {"Client"},
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
     */
    public function deleteAction(Request $request)
    {
        $this->clientService->handleDelete($request);
    }

    /**
     * Update a BileMo's client
     * 
     * @Rest\Put(
     *      path = "/api/clients/{id}",
     *      name = "app_clients_update",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      StatusCode = 200
     * )
     * @IsGranted("MANAGE", subject="client")
     * 
     * @SWG\Put(
     *     description="Update a BileMo's client (Restricted to admin)",
     *     tags = {"Client"},
     *     @SWG\Response(
     *          response=201,
     *          description="Successful operation: Updated",
     *          @Model(type=Client::class)
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
     *          description="Client's characteristics to be updated",
     *          @SWG\Schema(
     *              type="array",
     *              @Model(type=Client::class)
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
    public function updateAction(Client $client, Request $request)
    {
        $updatedClient = $this->clientService->handleUpdate($client, $request);
        return $updatedClient;
    }
}
