<?php

declare(strict_types=1);
namespace Symplify\MarkdownDiff\ValueObject;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\MarkdownDiff\DependencyInjection\Extension\ValueObject\MarkdownDiffExtension;
final class MarkdownDiffBundle extends Bundle
{
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new MarkdownDiffExtension();
    }
}
