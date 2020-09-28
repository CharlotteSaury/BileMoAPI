<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * Manage entity list pagination
     *
     * @param array $data
     * @param integer $page
     * @param integer $limit
     * @param string $route
     * @return PaginatedRepresentation
     */
    protected function paginate(array $data, int $page, int $limit, string $route)
    {
        if (0 >= $page || 0 >= $limit) {
            throw new \LogicException('Page and limit parameters can\'t be inferior to 1');
        }

        $adapter = new ArrayAdapter($data);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setCurrentPage($page);
        $pagerfanta->setMaxPerPage((int) $limit);
        $numberOfPages = $pagerfanta->getNbPages();

        $paginatedRepresentation = new PaginatedRepresentation(
            new CollectionRepresentation($pagerfanta->getCurrentPageResults()),
            $route,
            [],
            $page,
            $limit,
            $numberOfPages,
            'page',
            'limit',
            true
        );

        return $paginatedRepresentation;
    }
}
