<?php

declare(strict_types=1);
namespace Symplify\EasyHydrator\Tests\HttpKernel\ValueObject;

use Rector\SimplePhpDocParser\Bundle\SimplePhpDocParserBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\EasyHydrator\ValueObject\EasyHydratorBundle;
use Symplify\SymplifyKernel\Bundle\ValueObject\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyHydratorTestKernel extends AbstractSymplifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config.php');
        parent::registerContainerConfiguration($loader);
    }
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new EasyHydratorBundle(), new SymplifyKernelBundle(), new SimplePhpDocParserBundle()];
    }
}
