<?php

declare(strict_types=1);
namespace Symplify\PackageScoper\HttpKernel\ValueObject;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorBundle;
use Symplify\SymplifyKernel\Bundle\ValueObject\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class PackageScoperKernel extends AbstractSymplifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
        parent::registerContainerConfiguration($loader);
    }
    /**
     * @return \Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorBundle[]|\Symplify\SymplifyKernel\Bundle\ValueObject\SymplifyKernelBundle[]
     */
    public function registerBundles(): iterable
    {
        return [new SymplifyKernelBundle(), new ComposerJsonManipulatorBundle()];
    }
}
