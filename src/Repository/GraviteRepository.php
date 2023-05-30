<?php

namespace App\Repository;

use App\Entity\Gravite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gravite>
 *
 * Repository des gravités.
 */
class GraviteRepository extends ServiceEntityRepository
{
    /**
     * GraviteRepository constructor.
     *
     * @param ManagerRegistry $registry Le registre de gestion des entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gravite::class);
    }

    /**
     * Ajoute une gravité.
     *
     * @param Gravite $entity L'entité de la gravité à ajouter.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function add(Gravite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une gravité.
     *
     * @param Gravite $entity L'entité de la gravité à supprimer.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function remove(Gravite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * Retourne un tableau d'objets Gravite.
//     *
//     * @param mixed $value La valeur utilisée pour filtrer les gravités.
//     * @return Gravite[] Un tableau d'objets Gravite.
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    /**
//     * Retourne un objet Gravite ou null.
//     *
//     * @param mixed $value La valeur utilisée pour filtrer la gravité.
//     * @return Gravite|null Un objet Gravite ou null.
//     */
//    public function findOneBySomeField($value): ?Gravite
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
