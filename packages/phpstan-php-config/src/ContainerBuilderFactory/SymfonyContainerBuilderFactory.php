<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\ContainerBuilderFactory;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PHPStanPHPConfig\Config\LoaderFactory\ImportLessPhpFileLoaderFactory;
use Symplify\PHPStanPHPConfig\DependencyInjection\ValueObject\MakeServicesPublicCompilerPass;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class SymfonyContainerBuilderFactory
{
    /**
     * @var ImportLessPhpFileLoaderFactory
     */
    private $importLessPhpFileLoaderFactory;

    public function __construct(ImportLessPhpFileLoaderFactory $importLessPhpFileLoaderFactory)
    {
        $this->importLessPhpFileLoaderFactory = $importLessPhpFileLoaderFactory;
    }

    public function createFromConfig(SmartFileInfo $phpConfigFileInfo): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();
        $fileLocator = new FileLocator($phpConfigFileInfo->getRealPathDirectory());

        $importLessPhpFileLoader = $this->importLessPhpFileLoaderFactory->create($containerBuilder, $fileLocator);
        $importLessPhpFileLoader->load($phpConfigFileInfo->getFilename());

        $containerBuilder->getCompilerPassConfig()
            ->setRemovingPasses([]);

        $containerBuilder->addCompilerPass(new MakeServicesPublicCompilerPass());

        $containerBuilder->reset();
        $containerBuilder->compile();

        return $containerBuilder;
    }
}
