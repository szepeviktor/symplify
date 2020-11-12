<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\HttpKernel\ValueObject;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorBundle;
use Symplify\ConsoleColorDiff\ValueObject\ConsoleColorDiffBundle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\ValueObject\AutowireInterfacesCompilerPass;
use Symplify\SymplifyKernel\Bundle\ValueObject\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class MonorepoBuilderKernel extends AbstractSymplifyKernel
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
        return [new ComposerJsonManipulatorBundle(), new SymplifyKernelBundle(), new ConsoleColorDiffBundle()];
    }
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireInterfacesCompilerPass([ReleaseWorkerInterface::class]));
    }
}
