<?php

namespace App\Factory;

use App\Entity\Criticite;
use App\Repository\CriticiteRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Criticite>
 *
 * @method        Criticite|Proxy create(array|callable $attributes = [])
 * @method static Criticite|Proxy createOne(array $attributes = [])
 * @method static Criticite|Proxy find(object|array|mixed $criteria)
 * @method static Criticite|Proxy findOrCreate(array $attributes)
 * @method static Criticite|Proxy first(string $sortedField = 'id')
 * @method static Criticite|Proxy last(string $sortedField = 'id')
 * @method static Criticite|Proxy random(array $attributes = [])
 * @method static Criticite|Proxy randomOrCreate(array $attributes = [])
 * @method static CriticiteRepository|RepositoryProxy repository()
 * @method static Criticite[]|Proxy[] all()
 * @method static Criticite[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Criticite[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Criticite[]|Proxy[] findBy(array $attributes)
 * @method static Criticite[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Criticite[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class CriticiteFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'libelle' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Criticite $criticite): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Criticite::class;
    }
}
