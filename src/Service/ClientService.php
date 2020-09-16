<?php

namespace App\Service;

use DateTime;
use App\Entity\Client;
use App\Handler\PaginationHandler;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Handler\ConstraintsViolationHandler;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ClientService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PaginationHandler
     */
    private $paginationHandler;

    /**
     * @var ConstraintsViolationHandler 
     */
    private $constraintsViolationHandler;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(PaginationHandler $paginationHandler, EntityManagerInterface $entityManager, ClientRepository $clientRepository, ConstraintsViolationHandler $constraintsViolationHandler, UserPasswordEncoderInterface $encoder)
    {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
        $this->paginationHandler = $paginationHandler;
        $this->constraintsViolationHandler = $constraintsViolationHandler;
        $this->encoder = $encoder;
    }

    public function handleList(ParamFetcherInterface $paramFetcher, Request $request)
    {
        $paginatedRepresentation = $this->paginationHandler->paginate(
            'client', 
            $paramFetcher->get('page'), 
            $paramFetcher->get('limit'), 
            $request->get('_route')
        );
        return $paginatedRepresentation;
    }

    public function handleDelete(Request $request)
    {
        $client = $this->clientRepository->findOneBy(['id' => $request->get('id')]);
        if ($client) {
            $this->entityManager->remove($client);
            $this->entityManager->flush();
        }
    }

    public function handleCreate(Client $client, ConstraintViolationList $violations)
    {
        $this->constraintsViolationHandler->validate($violations);

        $client->setRoles(['ROLE_USER']);
        $client->setCreatedAt(new DateTime());
        $hashedPassword = $this->encoder->encodePassword($client, $client->getPassword());
        $client->setPassword($hashedPassword);
        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $client;
    }

    public function handleUpdate(Client $client, Request $request)
    {
        $data = json_decode($request->getContent());

        foreach ($data as $key => $value) {
            if ($key && !empty($value)) {
                if ($key == 'password') {
                    $hashedPassword = $this->encoder->encodePassword($client, $value);
                    $client->setPassword($hashedPassword);
                } elseif (in_array($key, ['email', 'company'])) {
                    $setter = 'set' . ucfirst($key);
                    $client->$setter($value);
                } else {
                    throw new AccessDeniedHttpException();
                }
            }
        }
        $this->entityManager->flush();
    }
}
