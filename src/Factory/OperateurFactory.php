<?php

namespace App\Factory;

use App\Entity\Operateur;
use App\Repository\OperateurRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Operateur>
 *
 * @method        Operateur|Proxy create(array|callable $attributes = [])
 * @method static Operateur|Proxy createOne(array $attributes = [])
 * @method static Operateur|Proxy find(object|array|mixed $criteria)
 * @method static Operateur|Proxy findOrCreate(array $attributes)
 * @method static Operateur|Proxy first(string $sortedField = 'id')
 * @method static Operateur|Proxy last(string $sortedField = 'id')
 * @method static Operateur|Proxy random(array $attributes = [])
 * @method static Operateur|Proxy randomOrCreate(array $attributes = [])
 * @method static OperateurRepository|RepositoryProxy repository()
 * @method static Operateur[]|Proxy[] all()
 * @method static Operateur[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Operateur[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Operateur[]|Proxy[] findBy(array $attributes)
 * @method static Operateur[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Operateur[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class OperateurFactory extends ModelFactory
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
            'email' => self::faker()->text(180),
            'nom' => self::faker()->text(255),
            'password' => self::faker()->text(),
            'prenom' => self::faker()->text(255),
            'roles' => [],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Operateur $operateur): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Operateur::class;
    }
}
