<?php

declare(strict_types=1);
namespace Symplify\CodingStandard\Tests\HttpKernel\ValueObject;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\CodingStandard\Bundle\ValueObject\SymplifyCodingStandardBundle;
use Symplify\ConsoleColorDiff\ValueObject\ConsoleColorDiffBundle;
use Symplify\EasyCodingStandard\Bundle\ValueObject\EasyCodingStandardBundle;
final class SymplifyCodingStandardKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
    }
    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/symplify_coding_standard';
    }
    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/symplify_coding_standard_log';
    }
    /**
     * @return \Symplify\CodingStandard\Bundle\ValueObject\SymplifyCodingStandardBundle[]|\Symplify\ConsoleColorDiff\ValueObject\ConsoleColorDiffBundle[]|\Symplify\EasyCodingStandard\Bundle\ValueObject\EasyCodingStandardBundle[]
     */
    public function registerBundles(): iterable
    {
        return [new SymplifyCodingStandardBundle(), new EasyCodingStandardBundle(), new ConsoleColorDiffBundle()];
    }
}
