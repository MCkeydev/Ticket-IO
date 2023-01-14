<?php

namespace App\Factory;

use App\Entity\Criticite;
use App\Entity\Gravite;
use App\Entity\Operateur;
use App\Entity\Service;
use App\Entity\Status;
use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Ticket>
 *
 * @method        Ticket|Proxy create(array|callable $attributes = [])
 * @method static Ticket|Proxy createOne(array $attributes = [])
 * @method static Ticket|Proxy find(object|array|mixed $criteria)
 * @method static Ticket|Proxy findOrCreate(array $attributes)
 * @method static Ticket|Proxy first(string $sortedField = 'id')
 * @method static Ticket|Proxy last(string $sortedField = 'id')
 * @method static Ticket|Proxy random(array $attributes = [])
 * @method static Ticket|Proxy randomOrCreate(array $attributes = [])
 * @method static TicketRepository|RepositoryProxy repository()
 * @method static Ticket[]|Proxy[] all()
 * @method static Ticket[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Ticket[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Ticket[]|Proxy[] findBy(array $attributes)
 * @method static Ticket[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Ticket[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class TicketFactory extends ModelFactory
{
    private EntityManagerInterface $entityManager;
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'description' => self::faker()->text(),
            'titre' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
             ->afterInstantiate(function(Ticket $ticket): void {
                 $operateur = $this->entityManager->getRepository(Operateur::class)->find(1);
                 $service = $this->entityManager->getRepository(Service::class)->find(1);

                 $client = $this->entityManager->getRepository(User::class)->find(2);
                 $status = $this->entityManager->getRepository(Status::class)->find(1);
                 $criticite = $this->entityManager->getRepository(Criticite::class)->find(1);
                 $gravite = $this->entityManager->getRepository(Gravite::class)->find(1);

                 $ticket->setClient($client);
                 $ticket->setCriticite($criticite);
                 $ticket->setService($service);
                 $ticket->setGravite($gravite);
                 $ticket->setStatus($status);
                 $ticket->setOperateur($operateur);

                 $this->entityManager->persist($ticket);

             })
        ;
    }

    protected static function getClass(): string
    {
        return Ticket::class;
    }
}
