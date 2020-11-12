<?php

declare(strict_types=1);
namespace Symplify\EasyCodingStandard\HttpKernel\ValueObject;

use Migrify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\CodingStandard\Bundle\ValueObject\SymplifyCodingStandardBundle;
use Symplify\ConsoleColorDiff\ValueObject\ConsoleColorDiffBundle;
use Symplify\EasyCodingStandard\Bundle\ValueObject\EasyCodingStandardBundle;
use Symplify\EasyCodingStandard\DependencyInjection\DelegatingLoaderFactory;
use Symplify\Skipper\Bundle\ValueObject\SkipperBundle;
use Symplify\SymplifyKernel\Bundle\ValueObject\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyCodingStandardKernel extends AbstractSymplifyKernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        $bundles = [new EasyCodingStandardBundle(), new SymplifyCodingStandardBundle(), new ConsoleColorDiffBundle(), new SymplifyKernelBundle(), new SkipperBundle()];
        if ($this->environment === 'test') {
            $bundles[] = new PhpConfigPrinterBundle();
        }
        return $bundles;
    }
    /**
     * @param ContainerInterface|ContainerBuilder $container
     */
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        $delegatingLoaderFactory = new DelegatingLoaderFactory();
        return $delegatingLoaderFactory->createFromContainerBuilderAndKernel($container, $this);
    }
}
