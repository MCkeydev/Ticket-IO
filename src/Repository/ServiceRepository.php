<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 *
 * Repository des services.
 */
class ServiceRepository extends ServiceEntityRepository
{
    /**
     * ServiceRepository constructor.
     *
     * @param ManagerRegistry $registry Le registre de gestion des entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    /**
     * Ajoute un service.
     *
     * @param Service $entity L'entité du service à ajouter.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function add(Service $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime un service.
     *
     * @param Service $entity L'entité du service à supprimer.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function remove(Service $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * Retourne un tableau d'objets Service.
//     *
//     * @param mixed $value La valeur utilisée pour filtrer les services.
//     * @return Service[] Un tableau d'objets Service.
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
//     * Retourne un objet Service ou null.
//     *
//     * @param mixed $value La valeur utilisée pour filtrer le service.
//     * @return Service|null Un objet Service ou null.
//     */
//    public function findOneBySomeField($value): ?Service
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
