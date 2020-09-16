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
     * @Rest\Get(
     *      path = "/api/clients/{id}",
     *      name = "app_clients_show",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      serializerGroups={"client"}
     * )
     * 
     * @Cache(maxage="3600", public=true, mustRevalidate=true)
     * 
     * IsGranted("MANAGE", subject="client")
     */
    public function showAction(Client $client)
    {
        return $client;
    }

    /**
     * @Rest\Get(
     *      path = "/api/clients",
     *      name = "app_clients_list"
     * )
     * @Rest\View()
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
     * IsGrante("ROLE_ADMIN")
     */
    public function listAction(ParamFetcherInterface $paramFetcher, Request $request)
    {
        return $this->clientService->handleList($paramFetcher, $request);
    }

    /**
     * @Rest\Post(
     *      path = "/api/clients",
     *      name = "app_clients_create"
     * )
     * @Rest\View(
     *      StatusCode = 201,
     *      serializerGroups={"client", "client_create"}
     * )
     * @ParamConverter("client", converter="fos_rest.request_body")
     * @IsGranted("ROLE_ADMIN")
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
     * @Rest\Delete(
     *      path = "/api/clients/{id}",
     *      name = "app_clients_delete",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      StatusCode = Response::HTTP_NO_CONTENT
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction(Request $request)
    {
        $this->clientService->handleDelete($request);
    }

    /**
     * @Rest\Put(
     *      path = "/api/clients/{id}",
     *      name = "app_clients_update",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      StatusCode = 200
     * )
     * @IsGranted("MANAGE", subject="client")
     */
    public function updateAction(Client $client, Request $request)
    {
        $updatedClient = $this->clientService->handleUpdate($client, $request);
        return $updatedClient;
    }
}
