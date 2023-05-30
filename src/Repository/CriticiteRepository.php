<?php

namespace App\Repository;

use App\Entity\Criticite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Criticite>
 *
 * Repository des criticités.
 */
class CriticiteRepository extends ServiceEntityRepository
{
    /**
     * CriticiteRepository constructor.
     *
     * @param ManagerRegistry $registry Le registre de gestion des entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Criticite::class);
    }

    /**
     * Ajoute une criticité.
     *
     * @param Criticite $entity L'entité de la criticité à ajouter.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function add(Criticite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une criticité.
     *
     * @param Criticite $entity L'entité de la criticité à supprimer.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function remove(Criticite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * Retourne un tableau d'objets Criticite.
//     *
//     * @param mixed $value La valeur utilisée pour filtrer les criticités.
//     * @return Criticite[] Un tableau d'objets Criticite.
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

//    /**
//     * Retourne un objet Criticite ou null.
//     *
//     * @param mixed $value La valeur utilisée pour filtrer la criticité.
//     * @return Criticite|null Un objet Criticite ou null.
//     */
//    public function findOneBySomeField($value): ?Criticite
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
