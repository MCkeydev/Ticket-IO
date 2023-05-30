<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 *
 * Repository des tickets.
 */
class TicketRepository extends ServiceEntityRepository
{
    /**
     * TicketRepository constructor.
     *
     * @param ManagerRegistry $registry Le registre de gestion des entités.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    /**
     * Ajoute un ticket.
     *
     * @param Ticket $entity L'entité du ticket à ajouter.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function add(Ticket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime un ticket.
     *
     * @param Ticket $entity L'entité du ticket à supprimer.
     * @param bool $flush Indique si les modifications doivent être persistées en base de données.
     */
    public function remove(Ticket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère tous les tickets correspondant à un service.
     *
     * @param int $serviceId L'ID du service.
     * @param int $statusId L'ID correspondant au statut du ticket souhaité.
     * @param bool $exclude Si exclude = true, renvoie tous les tickets n'ayant pas le statut spécifié.
     * @param int $batch Le numéro du lot de résultats à récupérer.
     * @param int $batchSize La taille du lot de résultats.
     * @return array Un tableau contenant les résultats paginés et le nombre total de résultats.
     */
    public function findServiceTickets(
        int $serviceId,
        int $statusId = 3,
        bool $exclude = true,
        int $batch = 1,
        int $batchSize = 500
    ): array {
        $query = $this->createQueryBuilder("t")
            ->andWhere("t.service = :val")
            ->setParameter("val", $serviceId)
            ->andWhere($exclude ? "t.status != :status" : "t.status = :status")
            ->setParameter("status", $statusId)
            ->orderBy("t.updatedAt", "DESC")
            ->setFirstResult(($batch - 1) * $batchSize)
            ->setMaxResults($batchSize);

        $paginatedResult = new Paginator($query, true);
        $count = count($paginatedResult);

        return ["results" => $paginatedResult, "total" => $count];
    }

    /**
     * Récupère tous les tickets correspondant à un statut spécifique.
     *
     * @param int $statusId L'ID correspondant au statut du ticket souhaité.
     * @param bool $exclude Si exclude = true, renvoie tous les tickets n'ayant pas le statut spécifié.
     * @param int $batch Le numéro du lot de résultats à récupérer.
     * @param int $batchSize La taille du lot de résultats.
     * @return array Un tableau contenant les résultats paginés et le nombre total de résultats.
     */
    public function findAllTickets(
        int $statusId = 3,
        bool $exclude = true,
        int $batch = 1,
        int $batchSize = 500
    ): array {
        $query = $this->createQueryBuilder("t")
            ->andWhere($exclude ? "t.status != :status" : "t.status = :status")
            ->setParameter("status", $statusId)
            ->orderBy("t.updatedAt", "DESC")
            ->setFirstResult(($batch - 1) * $batchSize)
            ->setMaxResults($batchSize);

        $paginatedResult = new Paginator($query, true);
        $count = count($paginatedResult);

        return ["results" => $paginatedResult, "total" => $count];
    }

    /**
     * Récupère tous les tickets d'un utilisateur.
     *
     * @param int $userId L'ID de l'utilisateur.
     * @param int $batch Le numéro du lot de résultats à récupérer.
     * @param int $batchSize La taille du lot de résultats.
     * @return array Un tableau contenant les résultats paginés et le nombre total de résultats.
     */
    public function findUserTickets(
        int $userId,
        int $batch = 1,
        int $batchSize = 500
    ): array {
        $query = $this->createQueryBuilder("t")
            ->andWhere("t.client = :userid")
            ->setParameter("userid", $userId)
            ->orderBy("t.createdAt", "DESC")
            ->setFirstResult(($batch - 1) * $batchSize)
            ->setMaxResults($batchSize);

        $paginatedResult = new Paginator($query, true);
        $totalResults = count($paginatedResult);

        return ["results" => $paginatedResult, "total" => $totalResults];
    }

    /**
     * Récupère tous les tickets d'un technicien correspondant à un statut spécifique.
     *
     * @param int $technicienId L'ID du technicien.
     * @param int $statusId L'ID correspondant au statut du ticket souhaité.
     * @param bool $exclude Si exclude = true, renvoie tous les tickets n'ayant pas le statut spécifié.
     * @param int $batch Le numéro du lot de résultats à récupérer.
     * @param int $batchSize La taille du lot de résultats.
     * @return array Un tableau contenant les résultats paginés et le nombre total de résultats.
     */
    public function findTechnicienTickets(
        int $technicienId,
        int $statusId = 3,
        bool $exclude = true,
        int $batch = 1,
        int $batchSize = 500
    ): array {
        $query = $this->createQueryBuilder("t")
            ->andWhere("t.technicien = :technicienid")
            ->setParameter("technicienid", $technicienId)
            ->andWhere($exclude ? "t.status != :status" : "t.status = :status")
            ->setParameter("status", $statusId)
            ->orderBy("t.updatedAt", "DESC")
            ->setFirstResult(($batch - 1) * $batchSize)
            ->setMaxResults($batchSize);

        $paginatedResult = new Paginator($query, true);
        $totalResults = count($paginatedResult);

        return ["results" => $paginatedResult, "total" => $totalResults];
    }
}
