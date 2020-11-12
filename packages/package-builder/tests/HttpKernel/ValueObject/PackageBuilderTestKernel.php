<?php

declare(strict_types=1);
namespace Symplify\PackageBuilder\Tests\HttpKernel\ValueObject;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\Parameter\ValueObject\ParameterProvider;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class PackageBuilderTestKernel extends AbstractSymplifyKernel
{
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->autowire(ParameterProvider::class)->setPublic(true);
    }
}
