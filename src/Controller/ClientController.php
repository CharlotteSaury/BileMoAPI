<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Repository\ClientRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class ClientController extends AbstractFOSRestController
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * @Rest\Get(
     *      path = "/clients/{id}",
     *      name = "app_clients_show",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      serializerGroups={"client"}
     * )
     */
    public function showAction(Client $client)
    {
        return $client;
    }

    /**
     * @Rest\Get(
     *      path = "/clients",
     *      name = "app_clients_list"
     * )
     * @Rest\View(
     *      serializerGroups={"client"}
     * )
     */
    public function listClients()
    {
        $clients = $this->clientRepository->findAll();
        return $clients;
    }
}