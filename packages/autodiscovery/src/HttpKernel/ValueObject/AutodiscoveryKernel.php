<?php

declare(strict_types=1);
namespace Symplify\Autodiscovery\HttpKernel\ValueObject;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class AutodiscoveryKernel extends AbstractSymplifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }
}
