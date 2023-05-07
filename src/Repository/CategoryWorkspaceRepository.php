<?php

namespace App\Repository;

use App\Entity\CategoryWorkspace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoryWorkspace>
 *
 * @method CategoryWorkspace|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryWorkspace|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryWorkspace[]    findAll()
 * @method CategoryWorkspace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryWorkspaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryWorkspace::class);
    }

    public function save(CategoryWorkspace $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CategoryWorkspace $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CategoryWorkspace[] Returns an array of CategoryWorkspace objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CategoryWorkspace
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
