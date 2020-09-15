<?php

namespace App\Controller;

use DateTime;
use Hateoas\Hateoas;
use App\Entity\Product;
use App\Form\ProductType;
use FOS\RestBundle\Context\Context;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use App\Handler\AuthorizationJsonHandler;
use FOS\RestBundle\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\Serializer\Serializer as SerializerSerializer;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProductController extends AbstractFOSRestController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AuthorizationJsonHandler
     */
    private $authorizationHandler;

    public function __construct(ProductRepository $productRepository, EntityManagerInterface $entityManager, AuthorizationJsonHandler $authorizationHandler)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
        $this->authorizationHandler = $authorizationHandler;
    }

    /**
     * @Rest\Get(
     *      path = "/api/products/{id}",
     *      name = "app_products_show",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      serializerGroups={"product"}
     * )
     */
    public function showAction(Product $product)
    {
        return $product;
    }

    /**
     * @Rest\Get(
     *      path = "/api/products",
     *      name = "app_products_list"
     * )
     * @Rest\View(
     *      serializerGroups={"products_list"}
     * )
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
        $paginatedRepresentation = $this->productRepository->search(
            $paramFetcher->get('page'),
            $paramFetcher->get('limit'),
            $request->get('_route')
        );
        $data = $serializer->serialize($paginatedRepresentation, 'json', SerializationContext::create()->setGroups(['Default', 'products_list']));
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Rest\Delete(
     *      path = "/api/products/{id}",
     *      name = "app_products_delete",
     *      requirements = {"id" = "\d+"}
     * )
     * @Rest\View(
     *      StatusCode = Response::HTTP_NO_CONTENT
     * )
     */
    public function deleteAction(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->authorizationHandler->forbiddenResponse('delete', 'product');
        }
        $product = $this->productRepository->findOneBy(['id' => $request->get('id')]);
        if ($product) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();
        }
    }

    /**
     * @Rest\Post(
     *      path = "/api/products",
     *      name = "app_products_create"
     * )
     * @Rest\View(
     *      StatusCode = 201,
     *      serializerGroups={"product"}
     * )
     * @ParamConverter(
     *      "product", 
     *      converter="fos_rest.request_body"
     * )
     */
    public function createAction(Product $product, ConstraintViolationList $violations)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->authorizationHandler->forbiddenResponse('add', 'product');
        }
        if (count($violations)) {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }

        $product->setCreatedAt(new DateTime());
        if ($product->getConfigurations() != null) {
            foreach ($product->getConfigurations() as $config) {
                $config->setProduct($product);
                foreach ($config->getImages() as $image) {
                    $image->setConfiguration($config);
                }
            }
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->view(
            $product,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_products_show', ['id' => $product->getId()], UrlGeneratorInterface::ABSOLUTE_URL)]
        );
    }
}
