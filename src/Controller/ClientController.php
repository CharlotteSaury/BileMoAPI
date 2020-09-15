<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Client;
use App\Repository\ClientRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use App\Handler\AuthorizationJsonHandler;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\ResourceValidationException;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
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
        if (!$this->isGranted('MANAGE', $client)) {
            return $this->authorizationHandler->forbiddenResponse('see', 'client');
        }

        return $client;
    }

    /**
     * @Rest\Get(
     *      path = "/api/clients",
     *      name = "app_clients_list"
     * )
     * @Rest\View()
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
    public function listAction(ParamFetcherInterface $paramFetcher, Request $request, SerializerInterface $serializer)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->authorizationHandler->forbiddenResponse('list', 'clients');
        }

        $paginatedRepresentation = $this->clientRepository->search(
            $paramFetcher->get('page'),
            $paramFetcher->get('limit'),
            $request->get('_route')
        );
        $data = $serializer->serialize($paginatedRepresentation, 'json', SerializationContext::create()->setGroups(['Default', 'clients_list']));
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
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
    public function createAction(Client $client, UserPasswordEncoderInterface $encoder, ConstraintViolationList $violations)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->authorizationHandler->forbiddenResponse('add', 'client');
        }
        /*if (count($violations)) {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }*/

        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
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
            ['Location' => $this->generateUrl('app_clients_show', ['id' => $client->getId()], UrlGeneratorInterface::ABSOLUTE_URL)]
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
     */
    public function deleteAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->authorizationHandler->forbiddenResponse('delete', 'client');
        }
        $client = $this->clientRepository->findOneBy(['id' => $request->get('id')]);
        if ($client) {
            $this->entityManager->remove($client);
            $this->entityManager->flush();
        }
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
     */
    public function updateAction(Client $client, Request $request, UserPasswordEncoderInterface $encoder)
    {
        if (!$this->isGranted('MANAGE', $client)) {
            return $this->authorizationHandler->forbiddenResponse('edit', 'client');
        }

        $data = json_decode($request->getContent());

        foreach ($data as $key => $value) {
            if ($key && !empty($value)) {
                if ($key == 'password') {
                    if ($this->getUser() != $client) {
                        return $this->authorizationHandler->forbiddenResponse('password');
                    } else {
                        $hashedPassword = $encoder->encodePassword($client, $value);
                        $client->setPassword($hashedPassword);
                    }
                } elseif (in_array($key, ['email', 'company'])) {
                    $setter = 'set' . ucfirst($key);
                    $client->$setter($value);
                } else {
                    return $this->authorizationHandler->forbiddenResponse('update', 'client', $key);
                }
            }
        }
        $this->entityManager->flush();

        return $client;
    }
}
