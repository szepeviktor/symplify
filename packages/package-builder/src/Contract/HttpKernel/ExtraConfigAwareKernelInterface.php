<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Contract\HttpKernel;

use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

interface ExtraConfigAwareKernelInterface extends KernelInterface
{
    /**
     * @param string[]|\Symplify\SmartFileSystem\ValueObject\SmartFileInfo[] $configs
     */
    public function setConfigs(array $configs): void;
}
