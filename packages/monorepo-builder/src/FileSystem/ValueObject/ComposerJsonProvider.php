<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\FileSystem\ValueObject;

use Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager;
use Symplify\MonorepoBuilder\Finder\ValueObject\PackageComposerFinder;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SymplifyKernel\ValueObject\ShouldNotHappenException;
final class ComposerJsonProvider
{
    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager
     */
    private $jsonFileManager;
    /**
     * @var \Symplify\MonorepoBuilder\Finder\ValueObject\PackageComposerFinder
     */
    private $packageComposerFinder;
    public function __construct(JsonFileManager $jsonFileManager, PackageComposerFinder $packageComposerFinder)
    {
        $this->jsonFileManager = $jsonFileManager;
        $this->packageComposerFinder = $packageComposerFinder;
    }
    public function getRootFileInfo(): SmartFileInfo
    {
        return $this->packageComposerFinder->getRootPackageComposerFile();
    }
    /**
     * @return mixed[]
     */
    public function getRootJson(): array
    {
        return $this->jsonFileManager->loadFromFilePath(getcwd() . '/composer.json');
    }
    /**
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    public function getPackagesComposerFileInfos(): array
    {
        return $this->packageComposerFinder->getPackageComposerFiles();
    }
    /**
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    public function getRootAndPackageFileInfos(): array
    {
        return array_merge($this->getPackagesComposerFileInfos(), [$this->packageComposerFinder->getRootPackageComposerFile()]);
    }
    public function getPackageFileInfoByName(string $packageName): SmartFileInfo
    {
        $packageComposerFiles = $this->packageComposerFinder->getPackageComposerFiles();
        foreach ($packageComposerFiles as $packageComposerFile) {
            $json = $this->jsonFileManager->loadFromFileInfo($packageComposerFile);
            if (!isset($json['name'])) {
                continue;
            }
            if ($json['name'] !== $packageName) {
                continue;
            }
            return $packageComposerFile;
        }
        throw new ShouldNotHappenException();
    }
}
