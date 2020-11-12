<?php

declare(strict_types=1);
namespace Symplify\EasyHydrator\ValueObject;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\ValueObject\AutowireArrayParameterCompilerPass;
use Symplify\EasyHydrator\DependencyInjection\Extension\ValueObject\EasyHydratorExtension;
final class EasyHydratorBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new EasyHydratorExtension();
    }
}
