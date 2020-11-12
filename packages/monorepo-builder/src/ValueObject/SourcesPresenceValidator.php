<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\ValueObject;

use Symplify\MonorepoBuilder\Exception\ValueObject\InvalidComposerJsonSetupException;
use Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ValueObject\ParameterProvider;
final class SourcesPresenceValidator
{
    /**
     * @var string[]
     */
    private $packageDirectories = [];
    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider
     */
    private $composerJsonProvider;
    public function __construct(ComposerJsonProvider $composerJsonProvider, ParameterProvider $parameterProvider)
    {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->packageDirectories = $parameterProvider->provideArrayParameter(Option::PACKAGE_DIRECTORIES);
    }
    public function validatePackageComposerJsons(): void
    {
        $composerPackageFiles = $this->composerJsonProvider->getPackagesComposerFileInfos();
        if (count($composerPackageFiles) > 0) {
            return;
        }
        throw new InvalidComposerJsonSetupException(sprintf('No package "composer.json" was found in package directories: "%s". Add "composer.json" or configure another directory in "parameters > package_directories"', implode('", "', $this->packageDirectories)));
    }
    public function validateRootComposerJsonName(): void
    {
        $mainComposerJson = $this->composerJsonProvider->getRootJson();
        if (isset($mainComposerJson['name'])) {
            return;
        }
        throw new InvalidComposerJsonSetupException('Complete "name" to your root "composer.json".');
    }
}
