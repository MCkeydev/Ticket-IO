<?php

namespace App\Repository;

use App\Entity\Technicien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Technicien>
 *
 * Repository des techniciens.
 */
class TechnicienRepository extends ServiceEntityRepository
{
    /**
     * TechnicienRepository constructor.
     *
     * @param ManagerRegistry $registry Le registre de gestion des entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technicien::class);
    }

    /**
     * Ajoute un technicien.
     *
     * @param Technicien $entity L'entité du technicien à ajouter.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function add(Technicien $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime un technicien.
     *
     * @param Technicien $entity L'entité du technicien à supprimer.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function remove(Technicien $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * Retourne un tableau d'objets Technicien.
    //     *
    //     * @param mixed $value La valeur utilisée pour filtrer les techniciens.
    //     * @return Technicien[] Un tableau d'objets Technicien.
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    /**
    //     * Retourne un objet Technicien ou null.
    //     *
    //     * @param mixed $value La valeur utilisée pour filtrer le technicien.
    //     * @return Technicien|null Un objet Technicien ou null.
    //     */
    //    public function findOneBySomeField($value): ?Technicien
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
