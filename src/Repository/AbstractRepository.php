<?php

namespace App\Repository;

use Pagerfanta\Pagerfanta;
use Hateoas\HateoasBuilder;
use Hateoas\Configuration\Route;
use Pagerfanta\Adapter\ArrayAdapter;
use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractRepository extends ServiceEntityRepository
{
    protected function paginate(Array $data, int $page, int $limit, string $route)
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