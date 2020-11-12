<?php

declare(strict_types=1);
namespace Symplify\ComposerJsonManipulator\Tests\HttpKernel\ValueObject;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorBundle;
use Symplify\SymplifyKernel\Bundle\ValueObject\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class ComposerJsonManipulatorKernel extends AbstractSymplifyKernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new ComposerJsonManipulatorBundle(), new SymplifyKernelBundle()];
    }
}
