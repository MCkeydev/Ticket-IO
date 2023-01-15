<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 *
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Ticket::class);
	}

	public function add(Ticket $entity, bool $flush = false): void
	{
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Ticket $entity, bool $flush = false): void
	{
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	/**
	 * Fetches all tickets corresponding to a service.
	 *
	 * @param  int $serviceId  Id of the service
	 * @param  int $statusId   Id corresponding to the desired ticket status
	 * @param  bool $exclude   If exclude = true, we want all tickets that don't have the above status.
	 * @return array
	 */
	public function findServiceTickets(
		int $serviceId,
		int $statusId = 3,
		bool $exclude = true,
		int $batch = 1,
		int $batchSize = 25
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
	 * Fetches all tickets corresponding to a specific status.
	 *
	 * @param  int $statusId
	 * @param  bool $exclude
	 * @return array
	 */
	public function findAllTickets(
		int $statusId = 3,
		bool $exclude = true,
		int $batch = 1,
		int $batchSize = 25
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

	public function findUserTickets(
		int $userId,
		int $batch = 1,
		int $batchSize = 25
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

	public function findTechnicienTickets(
		int $technicienId,
		int $statusId = 3,
		bool $exclude = true,
		int $batch = 1,
		int $batchSize = 25
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
