<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ComposerJsonManipulator\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ValueObject\ParameterProvider;
use Symplify\PackageBuilder\Reflection\ValueObject\PrivatesCaller;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::INLINE_SECTIONS, ['keywords']);

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\ComposerJsonManipulator\\', __DIR__ . '/../src');

    $services->set(SmartFileSystem::class);
    $services->set(PrivatesCaller::class);

    $services->set(ParameterProvider::class)
        ->args([ref(ContainerInterface::class)]);
};
