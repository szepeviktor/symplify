<?php

declare(strict_types=1);
namespace Symplify\ComposerJsonManipulator\ValueObject;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ComposerJsonManipulator\DependencyInjection\Extension\ValueObject\ComposerJsonManipulatorExtension;
final class ComposerJsonManipulatorBundle extends Bundle
{
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new ComposerJsonManipulatorExtension();
    }
}
