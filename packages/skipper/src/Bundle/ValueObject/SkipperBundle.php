<?php

declare(strict_types=1);
namespace Symplify\Skipper\Bundle\ValueObject;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\Skipper\DependencyInjection\Extension\ValueObject\SkipperExtension;
final class SkipperBundle extends Bundle
{
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new SkipperExtension();
    }
}
