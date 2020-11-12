<?php

declare(strict_types=1);
namespace Symplify\Skipper\HttpKernel\ValueObject;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\Skipper\Bundle\ValueObject\SkipperBundle;
use Symplify\SymplifyKernel\Bundle\ValueObject\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class SkipperKernel extends AbstractSymplifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
        parent::registerContainerConfiguration($loader);
    }
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new SkipperBundle(), new SymplifyKernelBundle()];
    }
}
