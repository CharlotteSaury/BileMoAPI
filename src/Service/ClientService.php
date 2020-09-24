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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator, PaginationHandler $paginationHandler, EntityManagerInterface $entityManager, ClientRepository $clientRepository, ConstraintsViolationHandler $constraintsViolationHandler, UserPasswordEncoderInterface $encoder)
    {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
        $this->paginationHandler = $paginationHandler;
        $this->constraintsViolationHandler = $constraintsViolationHandler;
        $this->encoder = $encoder;
        $this->validator = $validator;
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
        dd($violations);
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
            if (in_array($key, Client::ATTRIBUTES)) {
                $setter = 'set' . ucfirst($key);
                $client->$setter($value);
            } else {
                throw new AccessDeniedHttpException();
            }
        }
        $errors = $this->validator->validate($client);
        for ($i = 0; $i < $errors->count(); $i++) {
            if ($errors->get($i)->getPropertyPath() === 'password') {
                $errors->remove($i);
            }
        }
        $this->constraintsViolationHandler->validate($errors);
        $this->entityManager->flush();
        return $client;
    }

    public function handlePasswordUpdate(Client $client, Request $request)
    {
        $passwordConstraint = new Assert\Length(["min" => 6, "max" => 30]);
        $data = json_decode($request->getContent(), true);

        if (array_key_exists('password', $data)) {
            $errors = $this->validator->validate(
                $data['password'],
                $passwordConstraint
            );
            $this->constraintsViolationHandler->validate($errors);

            $hashedPassword = $this->encoder->encodePassword($client, $data['password']);
            $client->setPassword($hashedPassword);

            $this->entityManager->flush();
            return $client;
        } else {
            throw new BadRequestException('Field password is missing.');
        }
    }
}
