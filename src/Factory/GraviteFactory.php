<?php

namespace App\Factory;

use App\Entity\Gravite;
use App\Repository\GraviteRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Gravite>
 *
 * @method        Gravite|Proxy create(array|callable $attributes = [])
 * @method static Gravite|Proxy createOne(array $attributes = [])
 * @method static Gravite|Proxy find(object|array|mixed $criteria)
 * @method static Gravite|Proxy findOrCreate(array $attributes)
 * @method static Gravite|Proxy first(string $sortedField = 'id')
 * @method static Gravite|Proxy last(string $sortedField = 'id')
 * @method static Gravite|Proxy random(array $attributes = [])
 * @method static Gravite|Proxy randomOrCreate(array $attributes = [])
 * @method static GraviteRepository|RepositoryProxy repository()
 * @method static Gravite[]|Proxy[] all()
 * @method static Gravite[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Gravite[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Gravite[]|Proxy[] findBy(array $attributes)
 * @method static Gravite[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Gravite[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class GraviteFactory extends ModelFactory
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
            // ->afterInstantiate(function(Gravite $gravite): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Gravite::class;
    }
}
