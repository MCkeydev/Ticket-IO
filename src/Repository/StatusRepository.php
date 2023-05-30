<?php

namespace App\Repository;

use App\Entity\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Status>
 *
 * Repository des statuts.
 */
class StatusRepository extends ServiceEntityRepository
{
    /**
     * StatusRepository constructor.
     *
     * @param ManagerRegistry $registry Le registre de gestion des entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Status::class);
    }

    /**
     * Ajoute un statut.
     *
     * @param Status $entity L'entité du statut à ajouter.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function add(Status $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime un statut.
     *
     * @param Status $entity L'entité du statut à supprimer.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function remove(Status $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * Retourne un tableau d'objets Status.
    //     *
    //     * @param mixed $value La valeur utilisée pour filtrer les statuts.
    //     * @return Status[] Un tableau d'objets Status.
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
    //     * Retourne un objet Status ou null.
    //     *
    //     * @param mixed $value La valeur utilisée pour filtrer le statut.
    //     * @return Status|null Un objet Status ou null.
    //     */
    //    public function findOneBySomeField($value): ?Status
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
