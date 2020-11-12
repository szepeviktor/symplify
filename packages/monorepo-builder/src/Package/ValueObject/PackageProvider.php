<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Package\ValueObject;

use Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager;
use Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider;
use Symplify\MonorepoBuilder\ValueObject\Package;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SymplifyKernel\ValueObject\ShouldNotHappenException;
final class PackageProvider
{
    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider
     */
    private $composerJsonProvider;
    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager
     */
    private $jsonFileManager;
    public function __construct(ComposerJsonProvider $composerJsonProvider, JsonFileManager $jsonFileManager)
    {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->jsonFileManager = $jsonFileManager;
    }
    /**
     * @return Package[]
     */
    public function provideWithTests(): array
    {
        return array_filter($this->provide(), function (Package $package): bool {
            return $package->hasTests();
        });
    }
    /**
     * @return Package[]
     */
    public function provide(): array
    {
        $packages = [];
        foreach ($this->composerJsonProvider->getPackagesComposerFileInfos() as $packagesComposerFileInfo) {
            $packageName = $this->detectNameFromFileInfo($packagesComposerFileInfo);
            $packages[] = new Package($packageName, $packagesComposerFileInfo);
        }
        usort($packages, function (Package $firstPackage, Package $secondPackage): int {
            return $firstPackage->getShortName() <=> $secondPackage->getShortName();
        });
        return $packages;
    }
    private function detectNameFromFileInfo(SmartFileInfo $smartFileInfo): string
    {
        $json = $this->jsonFileManager->loadFromFileInfo($smartFileInfo);
        if (!isset($json['name'])) {
            throw new ShouldNotHappenException();
        }
        return (string) $json['name'];
    }
}
