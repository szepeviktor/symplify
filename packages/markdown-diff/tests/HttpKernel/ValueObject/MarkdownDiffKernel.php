<?php

declare(strict_types=1);
namespace Symplify\MarkdownDiff\Tests\HttpKernel\ValueObject;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\MarkdownDiff\ValueObject\MarkdownDiffBundle;
use Symplify\SymplifyKernel\Bundle\ValueObject\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class MarkdownDiffKernel extends AbstractSymplifyKernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new MarkdownDiffBundle(), new SymplifyKernelBundle()];
    }
}
