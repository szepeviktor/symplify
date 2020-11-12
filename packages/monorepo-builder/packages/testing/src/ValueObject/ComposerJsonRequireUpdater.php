<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Testing\ValueObject;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager;
use Symplify\ConsoleColorDiff\Console\Output\ValueObject\ConsoleDiffer;
use Symplify\MonorepoBuilder\Testing\ComposerJson\ValueObject\ComposerVersionManipulator;
use Symplify\MonorepoBuilder\Testing\PackageDependency\ValueObject\UsedPackagesResolver;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
final class ComposerJsonRequireUpdater
{
    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager
     */
    private $jsonFileManager;
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @var \Symplify\MonorepoBuilder\Testing\ComposerJson\ValueObject\ComposerVersionManipulator
     */
    private $composerVersionManipulator;
    /**
     * @var \Symplify\MonorepoBuilder\Testing\PackageDependency\ValueObject\UsedPackagesResolver
     */
    private $usedPackagesResolver;
    /**
     * @var \Symplify\ConsoleColorDiff\Console\Output\ValueObject\ConsoleDiffer
     */
    private $consoleDiffer;
    public function __construct(JsonFileManager $jsonFileManager, \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, ComposerVersionManipulator $composerVersionManipulator, UsedPackagesResolver $usedPackagesResolver, ConsoleDiffer $consoleDiffer)
    {
        $this->jsonFileManager = $jsonFileManager;
        $this->symfonyStyle = $symfonyStyle;
        $this->composerVersionManipulator = $composerVersionManipulator;
        $this->usedPackagesResolver = $usedPackagesResolver;
        $this->consoleDiffer = $consoleDiffer;
    }
    public function processPackage(SmartFileInfo $packageFileInfo): void
    {
        $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageFileInfo);
        $usedPackageNames = $this->usedPackagesResolver->resolveForPackage($packageComposerJson);
        if ($usedPackageNames === []) {
            $message = sprintf('Package "%s" does not use any mutual dependencies, so we skip it', $packageFileInfo->getRelativeFilePathFromCwd());
            $this->symfonyStyle->note($message);
            return;
        }
        $packageComposerJson = $this->composerVersionManipulator->setAsteriskVersionForUsedPackages($packageComposerJson, $usedPackageNames);
        $oldComposerJsonContents = $packageFileInfo->getContents();
        $newComposerJsonContents = $this->jsonFileManager->printJsonToFileInfo($packageComposerJson, $packageFileInfo);
        $message = sprintf('File "%s" was updated', $packageFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->title($message);
        $this->consoleDiffer->diffAndPrint($oldComposerJsonContents, $newComposerJsonContents);
        $this->symfonyStyle->newLine(2);
    }
}
