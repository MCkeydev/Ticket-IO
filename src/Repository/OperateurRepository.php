<?php

namespace App\Repository;

use App\Entity\Operateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Operateur>
 *
 * Repository des opérateurs.
 */
class OperateurRepository extends ServiceEntityRepository
{
    /**
     * OperateurRepository constructor.
     *
     * @param ManagerRegistry $registry Le registre de gestion des entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operateur::class);
    }

    /**
     * Ajoute un opérateur.
     *
     * @param Operateur $entity L'entité de l'opérateur à ajouter.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function add(Operateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime un opérateur.
     *
     * @param Operateur $entity L'entité de l'opérateur à supprimer.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function remove(Operateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * Retourne un tableau d'objets Operateur.
//     *
//     * @param mixed $value La valeur utilisée pour filtrer les opérateurs.
//     * @return Operateur[] Un tableau d'objets Operateur.
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    /**
//     * Retourne un objet Operateur ou null.
//     *
//     * @param mixed $value La valeur utilisée pour filtrer l'opérateur.
//     * @return Operateur|null Un objet Operateur ou null.
//     */
//    public function findOneBySomeField($value): ?Operateur
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
