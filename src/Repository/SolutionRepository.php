<?php

namespace App\Repository;

use App\Entity\Solution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Solution>
 *
 * Repository des solutions.
 */
class SolutionRepository extends ServiceEntityRepository
{
    /**
     * SolutionRepository constructor.
     *
     * @param ManagerRegistry $registry Le registre de gestion des entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Solution::class);
    }

    /**
     * Ajoute une solution.
     *
     * @param Solution $entity L'entité de la solution à ajouter.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function add(Solution $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une solution.
     *
     * @param Solution $entity L'entité de la solution à supprimer.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function remove(Solution $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * Retourne un tableau d'objets Solution.
    //     *
    //     * @param mixed $value La valeur utilisée pour filtrer les solutions.
    //     * @return Solution[] Un tableau d'objets Solution.
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    /**
    //     * Retourne un objet Solution ou null.
    //     *
    //     * @param mixed $value La valeur utilisée pour filtrer la solution.
    //     * @return Solution|null Un objet Solution ou null.
    //     */
    //    public function findOneBySomeField($value): ?Solution
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
