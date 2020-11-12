<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class EasyCodingStandardExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        $config = __DIR__ . '/../../../config';
        if (! realpath($config)) {
            $config = __DIR__ . '/../../../../config';
        }

        // needed for parameter shifting of sniff/fixer params
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(

            $config

        ));

        $phpFileLoader->load('config.php');
    }
}
