<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Customer;
use Doctrine\Persistence\ManagerRegistry;
use Hateoas\Representation\PaginatedRepresentation;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /**
     * Get paginated customers list regarding research parameters
     *
     * @param integer $page
     * @param integer $limit
     * @param string $route
     * @param Client $client
     * @return PaginatedRepresentation
     */
    public function search(int $page, int $limit, string $route, Client $client)
    {
        $builder = $this
            ->createQueryBuilder('c')
            ->leftJoin('c.clients', 'clients')
            ->andWhere('clients = :val')
            ->setParameter('val', $client)
            ;

        return $this->paginate($builder->getQuery()->getResult(), $page, $limit, $route);
    }

    // /**
    //  * @return Customer[] Returns an array of Customer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Customer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
