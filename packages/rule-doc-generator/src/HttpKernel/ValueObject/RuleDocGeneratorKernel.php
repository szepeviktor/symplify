<?php

declare(strict_types=1);
namespace Symplify\RuleDocGenerator\HttpKernel\ValueObject;

use Migrify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\MarkdownDiff\ValueObject\MarkdownDiffBundle;
use Symplify\SymplifyKernel\Bundle\ValueObject\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class RuleDocGeneratorKernel extends AbstractSymplifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new SymplifyKernelBundle(), new MarkdownDiffBundle(), new PhpConfigPrinterBundle()];
    }
}
