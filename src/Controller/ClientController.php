<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Handler\AuthorizationJsonHandler;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

    /**
     * @var AuthorizationJsonHandler
     */
    private $authorizationHandler;

    public function __construct(ClientRepository $clientRepository, EntityManagerInterface $entityManager, AuthorizationJsonHandler $authorizationHandler)
    {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
        $this->authorizationHandler = $authorizationHandler;
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
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->authorizationHandler->forbiddenResponse('see', 'client');
        }

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
    public function listAction()
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->authorizationHandler->forbiddenResponse('list', 'clients');
        }

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
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->authorizationHandler->forbiddenResponse('add', 'client');
        }

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

    /**
     * @Rest\Delete(
     *      path = "/api/clients/{id}",
     *      name = "app_clients_delete",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      StatusCode = 204
     * )
     */
    public function deleteAction(Client $client) 
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->authorizationHandler->forbiddenResponse('delete', 'client');
        }

        $this->entityManager->remove($client);
        $this->entityManager->flush();
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}