<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function search(string $q): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :q OR p.description LIKE :q')
            ->setParameter('q', '%' . $q . '%')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTopSellers(int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->select('p, SUM(oi.quantity) AS HIDDEN total')
            ->join('p.orderItems', 'oi')
            ->groupBy('p.id')
            ->orderBy('total', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getBestsellerIds(): array
    {
        $results = $this->createQueryBuilder('p')
            ->select('p.id')
            ->where('p.isBestseller = true')
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'id');
    }
}
