<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\ValueObject;

use Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
/**
 * @see \Symplify\MonorepoBuilder\Tests\DevMasterAliasUpdater\DevMasterAliasUpdaterTest
 */
final class DevMasterAliasUpdater
{
    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager
     */
    private $jsonFileManager;
    public function __construct(JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }
    /**
     * @param \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[] $fileInfos
     */
    public function updateFileInfosWithAlias(array $fileInfos, string $alias): void
    {
        foreach ($fileInfos as $fileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($fileInfo);
            if ($this->shouldSkip($json, $alias)) {
                continue;
            }
            $json['extra']['branch-alias']['dev-master'] = $alias;
            $this->jsonFileManager->printJsonToFileInfo($json, $fileInfo);
        }
    }
    /**
     * @param mixed[] $json
     */
    private function shouldSkip(array $json, string $alias): bool
    {
        // update only when already present
        if (!isset($json['extra']['branch-alias']['dev-master'])) {
            return true;
        }
        $currentAlias = $json['extra']['branch-alias']['dev-master'];
        return $currentAlias === $alias;
    }
}
