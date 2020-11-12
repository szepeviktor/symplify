<?php

declare(strict_types=1);
namespace Symplify\Autodiscovery\Yaml\ValueObject;

use Nette\Utils\Strings;
use Symplify\Autodiscovery\ValueObject\ServiceConfig;
/**
 * @see \Symplify\Autodiscovery\Tests\Yaml\CommonNamespaceResolverTest
 */
final class CommonNamespaceResolver
{
    /**
     * @return string[]
     */
    public function resolve(ServiceConfig $serviceConfig, int $nestingLevel): array
    {
        $classes = $serviceConfig->getClasses();
        if ($classes === []) {
            return [];
        }
        $namespaces = [];
        foreach ($classes as $class) {
            $namespace = Strings::before($class, '\\', $nestingLevel);
            if ($namespace) {
                $namespaces[] = $namespace;
            }
        }
        if (count($namespaces) > 0) {
            return array_unique($namespaces);
        }
        // reiterate with less strict nesting
        return $this->resolve($serviceConfig, --$nestingLevel);
    }
}
