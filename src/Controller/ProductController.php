<?php

namespace App\Controller;

use DateTime;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Handler\AuthorizationJsonHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
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
     *      serializerGroups={"product"}
     * )
     */
    public function listAction()
    {
        $products = $this->productRepository->findAll();
        return $products;
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
     * @ParamConverter("product", converter="fos_rest.request_body")
     */
    public function createAction(Product $product)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->authorizationHandler->forbiddenResponse('add', 'product');
        }

        $product->setCreatedAt(new DateTime());
        $product->setUpdatedAt($product->getCreatedAt());
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->view(
            $product,
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_products_show', ['id' => $product->getId(), UrlGeneratorInterface::ABSOLUTE_URL])]
        );
    }
}
