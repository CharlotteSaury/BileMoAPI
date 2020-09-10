<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Repository\ClientRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ClientController extends AbstractFOSRestController
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ClientRepository $clientRepository, EntityManagerInterface $entityManager)
    {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
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
     * @Rest\View(
     *      serializerGroups={"client"}
     * )
     */
    public function listClients()
    {
        $clients = $this->clientRepository->findAll();
        return $clients;
    }

    /**
     * @Rest\Post(
     *      path = "/api/clients",
     *      name = "app_clients_create"
     * )
     * @Rest\View(
     *      StatusCode = 201,
     *      serializerGroups={"client"}
     * )
     * @ParamConverter("client", converter="fos_rest.request_body")
     */
    public function createAction(Client $client, UserPasswordEncoderInterface $encoder)
    {
        $client->setRoles(['ROLE_USER']);
        $client->setCreatedAt(new DateTime());
        $hashedPassword = $encoder->encodePassword($client, $client->getPassword());
        $client->setPassword($hashedPassword);
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $this->view(
            $client,
            Response::HTTP_CREATED, 
            ['Location' => $this->generateUrl('app_clients_show', ['id' => $client->getId(), UrlGeneratorInterface::ABSOLUTE_URL])]
        );
    }
}