<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Package\ValueObject;

use Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager;
use Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
final class PackageNamesProvider
{
    /**
     * @var string[]
     */
    private $names = [];
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
     * @return string[]
     */
    public function provide(): array
    {
        if ($this->names !== []) {
            return $this->names;
        }
        $packagesFileInfos = $this->composerJsonProvider->getPackagesComposerFileInfos();
        foreach ($packagesFileInfos as $packagesFileInfo) {
            $name = $this->extractNameFromFileInfo($packagesFileInfo);
            if ($name !== null) {
                $this->names[] = $name;
            }
        }
        return $this->names;
    }
    private function extractNameFromFileInfo(SmartFileInfo $smartFileInfo): ?string
    {
        $json = $this->jsonFileManager->loadFromFileInfo($smartFileInfo);
        return $json['name'] ?? null;
    }
}
