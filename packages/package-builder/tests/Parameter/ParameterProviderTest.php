<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Parameter;

use Symplify\PackageBuilder\Parameter\ValueObject\ParameterProvider;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PackageBuilder\Tests\HttpKernel\ValueObject\PackageBuilderTestKernel;

final class ParameterProviderTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->bootKernelWithConfigs(
            PackageBuilderTestKernel::class,
            [__DIR__ . '/ParameterProviderSource/config.yml']
        );

        $parameterProvider = self::$container->get(ParameterProvider::class);

        $parameters = $parameterProvider->provide();
        $this->assertArrayHasKey('key', $parameters);
        $this->assertArrayHasKey('camelCase', $parameters);
        $this->assertArrayHasKey('pascal_case', $parameters);

        $this->assertSame('value', $parameters['key']);
        $this->assertSame('Lion', $parameters['camelCase']);
        $this->assertSame('Celsius', $parameters['pascal_case']);

        $this->assertSame('value', $parameterProvider->provideParameter('key'));

        $parameterProvider->changeParameter('key', 'anotherKey');
        $this->assertSame('anotherKey', $parameterProvider->provideParameter('key'));
    }

    public function testIncludingYaml(): void
    {
        $this->bootKernelWithConfigs(
            PackageBuilderTestKernel::class,
            [__DIR__ . '/ParameterProviderSource/Yaml/including-config.php']
        );

        $parameterProvider = self::$container->get(ParameterProvider::class);

        $parameters = $parameterProvider->provide();
        $this->assertArrayHasKey('one', $parameters);
        $this->assertArrayHasKey('two', $parameters);

        $this->assertSame(1, $parameters['one']);
        $this->assertSame(2, $parameters['two']);

        $this->assertArrayHasKey('kernel.project_dir', $parameterProvider->provide());
    }
}
