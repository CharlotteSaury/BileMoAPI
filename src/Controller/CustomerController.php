<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Handler\AuthorizationJsonHandler;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\ConstraintViolationList;

class CustomerController extends AbstractFOSRestController
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AuthorizationJsonHandler
     */
    private $authorizationHandler;

    public function __construct(CustomerRepository $customerRepository, EntityManagerInterface $entityManager, AuthorizationJsonHandler $authorizationHandler)
    {
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
        $this->authorizationHandler = $authorizationHandler;
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
     */
    public function showAction(Customer $customer)
    {
        if (!$this->isGranted('MANAGE', $customer)) {
            return $this->authorizationHandler->forbiddenResponse('see', 'client');
        }
        return $customer;
    }

    /**
     * @Rest\Get(
     *      path = "/api/customers",
     *      name = "app_customers_list"
     * )
     * @Rest\View(
     *      serializerGroups={"customer"}
     * )
     */
    public function listAction()
    {
        $customers = $this->customerRepository->findBy(['client' => $this->getUser()]);
        return $customers;
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
        if (count($violations)) {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }
        $customers = $this->customerRepository->findBy(['client' => $this->getUser()]);

        foreach ($customers as $currentCustomer) {
            if ($currentCustomer->getEmail() === $customer->getEmail()) {
                return new JsonResponse([
                    'code' => 400,
                    'message' => 'This customer is already associated to this client'
                ],
                Response::HTTP_BAD_REQUEST
            );
            }
        }

        $customer->setClient($this->getUser());
        $customer->setCreatedAt(new DateTime());
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return $this->view(
            $customer,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_customers_show', ['id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL)]
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
        $customer = $this->customerRepository->findOneBy(['id' => $request->get('id')]);
        
        if ($customer) {
            if (!$this->isGranted('MANAGE', $customer)) {
                return $this->authorizationHandler->forbiddenResponse('delete', 'customer');
            }
            $this->entityManager->remove($customer);
            $this->entityManager->flush();
        }
    }
}
