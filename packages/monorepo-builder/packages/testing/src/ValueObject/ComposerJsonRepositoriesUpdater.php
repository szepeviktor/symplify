<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Testing\ValueObject;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager;
use Symplify\ConsoleColorDiff\Console\Output\ValueObject\ConsoleDiffer;
use Symplify\MonorepoBuilder\Package\ValueObject\PackageNamesProvider;
use Symplify\MonorepoBuilder\Testing\ComposerJson\ValueObject\ComposerJsonSymlinker;
use Symplify\MonorepoBuilder\Testing\PackageDependency\ValueObject\UsedPackagesResolver;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
final class ComposerJsonRepositoriesUpdater
{
    /**
     * @var \Symplify\MonorepoBuilder\Package\ValueObject\PackageNamesProvider
     */
    private $packageNamesProvider;
    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager
     */
    private $jsonFileManager;
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var \Symplify\MonorepoBuilder\Testing\ComposerJson\ValueObject\ComposerJsonSymlinker
     */
    private $composerJsonSymlinker;
    /**
     * @var \Symplify\MonorepoBuilder\Testing\PackageDependency\ValueObject\UsedPackagesResolver
     */
    private $usedPackagesResolver;
    /**
     * @var \Symplify\ConsoleColorDiff\Console\Output\ValueObject\ConsoleDiffer
     */
    private $consoleDiffer;
    public function __construct(PackageNamesProvider $packageNamesProvider, JsonFileManager $jsonFileManager, \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, ComposerJsonSymlinker $composerJsonSymlinker, UsedPackagesResolver $usedPackagesResolver, ConsoleDiffer $consoleDiffer)
    {
        $this->packageNamesProvider = $packageNamesProvider;
        $this->jsonFileManager = $jsonFileManager;
        $this->symfonyStyle = $symfonyStyle;
        $this->composerJsonSymlinker = $composerJsonSymlinker;
        $this->usedPackagesResolver = $usedPackagesResolver;
        $this->consoleDiffer = $consoleDiffer;
    }
    public function processPackage(SmartFileInfo $packageFileInfo, SmartFileInfo $mainComposerJsonFileInfo): void
    {
        $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageFileInfo);
        $usedPackageNames = $this->usedPackagesResolver->resolveForPackage($packageComposerJson);
        if ($usedPackageNames === []) {
            $message = sprintf('Package "%s" does not use any mutual dependencies, so we skip it', $packageFileInfo->getRelativeFilePathFromCwd());
            $this->symfonyStyle->note($message);
            return;
        }
        // possibly replace them all to cover recursive secondary dependencies
        $packageNames = $this->packageNamesProvider->provide();
        $oldComposerJsonContents = $packageFileInfo->getContents();
        $packageComposerJson = $this->composerJsonSymlinker->decoratePackageComposerJsonWithPackageSymlinks($packageComposerJson, $packageNames, $mainComposerJsonFileInfo);
        $newComposerJsonContents = $this->jsonFileManager->printJsonToFileInfo($packageComposerJson, $packageFileInfo);
        $message = sprintf('File "%s" was updated', $packageFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->title($message);
        $this->consoleDiffer->diffAndPrint($oldComposerJsonContents, $newComposerJsonContents);
        $this->symfonyStyle->newLine(2);
    }
}
