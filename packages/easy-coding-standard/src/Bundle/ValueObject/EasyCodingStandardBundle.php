<?php

declare(strict_types=1);
namespace Symplify\EasyCodingStandard\Bundle\ValueObject;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ValueObject\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ValueObject\FixerWhitespaceConfigCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ValueObject\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ValueObject\RemoveMutualCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\Extension\ValueObject\EasyCodingStandardExtension;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\ValueObject\AutowireInterfacesCompilerPass;
final class EasyCodingStandardBundle extends Bundle
{
    /**
     * Order of compiler passes matters!
     */
    public function build(ContainerBuilder $containerBuilder): void
    {
        // cleanup
        $containerBuilder->addCompilerPass(new RemoveExcludedCheckersCompilerPass());
        $containerBuilder->addCompilerPass(new RemoveMutualCheckersCompilerPass());
        // autowire
        $containerBuilder->addCompilerPass(new AutowireInterfacesCompilerPass([FixerInterface::class, Sniff::class, OutputFormatterInterface::class]));
        // exceptions
        $containerBuilder->addCompilerPass(new ConflictingCheckersCompilerPass());
        // method calls
        $containerBuilder->addCompilerPass(new FixerWhitespaceConfigCompilerPass());
    }
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new EasyCodingStandardExtension();
    }
}
