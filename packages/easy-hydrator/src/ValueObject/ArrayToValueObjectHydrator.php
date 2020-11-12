<?php

declare(strict_types=1);
namespace Symplify\EasyHydrator\ValueObject;

use Symfony\Contracts\Cache\CacheInterface;
/**
 * @see \Symplify\EasyHydrator\Tests\ArrayToValueObjectHydratorTest
 */
final class ArrayToValueObjectHydrator
{
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var \Symplify\EasyHydrator\ValueObject\ClassConstructorValuesResolver
     */
    private $classConstructorValuesResolver;
    public function __construct(\Symfony\Contracts\Cache\CacheInterface $cache, ClassConstructorValuesResolver $classConstructorValuesResolver)
    {
        $this->cache = $cache;
        $this->classConstructorValuesResolver = $classConstructorValuesResolver;
    }
    /**
     * @param mixed[] $data
     */
    public function hydrateArray(array $data, string $class): object
    {
        $arrayHash = md5(serialize($data) . $class);
        return $this->cache->get($arrayHash, function () use ($class, $data) {
            $resolveClassConstructorValues = $this->classConstructorValuesResolver->resolve($class, $data);
            return new $class(...$resolveClassConstructorValues);
        });
    }
    /**
     * @param mixed[][] $datas
     * @return object[]
     */
    public function hydrateArrays(array $datas, string $class): array
    {
        $valueObjects = [];
        foreach ($datas as $data) {
            $valueObjects[] = $this->hydrateArray($data, $class);
        }
        return $valueObjects;
    }
}
