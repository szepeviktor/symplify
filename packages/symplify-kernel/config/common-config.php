<?php

declare(strict_types=1);

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager;
use Symplify\ComposerJsonManipulator\Json\ValueObject\JsonCleaner;
use Symplify\ComposerJsonManipulator\Json\ValueObject\JsonInliner;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\PackageBuilder\Parameter\ValueObject\ParameterProvider;
use Symplify\PackageBuilder\Reflection\ValueObject\PrivatesAccessor;
use Symplify\SmartFileSystem\ValueObject\FileSystemFilter;
use Symplify\SmartFileSystem\ValueObject\FileSystemGuard;
use Symplify\SmartFileSystem\Finder\ValueObject\FinderSanitizer;
use Symplify\SmartFileSystem\Finder\ValueObject\SmartFinder;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;
use Symplify\SymplifyKernel\Console\ConsoleApplicationFactory;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    // symfony style
    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)
        ->factory([ref(SymfonyStyleFactory::class), 'create']);

    // filesystem
    $services->set(FinderSanitizer::class);
    $services->set(SmartFileSystem::class);
    $services->set(SmartFinder::class);
    $services->set(FileSystemGuard::class);
    $services->set(FileSystemFilter::class);

    $services->set(ParameterProvider::class)
        ->args([ref(ContainerInterface::class)]);

    $services->set(PrivatesAccessor::class);

    $services->set(ConsoleApplicationFactory::class);

    // composer json factory
    $services->set(ComposerJsonFactory::class);
    $services->set(JsonFileManager::class);
    $services->set(JsonCleaner::class);
    $services->set(JsonInliner::class);
};
