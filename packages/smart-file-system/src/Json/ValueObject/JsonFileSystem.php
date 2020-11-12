<?php

declare(strict_types=1);
namespace Symplify\SmartFileSystem\Json\ValueObject;

use Nette\Utils\Arrays;
use Nette\Utils\Json;
use Symplify\SmartFileSystem\ValueObject\FileSystemGuard;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;
/**
 * @see \Symplify\SmartFileSystem\Tests\Json\JsonFileSystem\JsonFileSystemTest
 */
final class JsonFileSystem
{
    /**
     * @var \Symplify\SmartFileSystem\ValueObject\FileSystemGuard
     */
    private $fileSystemGuard;
    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(FileSystemGuard $fileSystemGuard, SmartFileSystem $smartFileSystem)
    {
        $this->fileSystemGuard = $fileSystemGuard;
        $this->smartFileSystem = $smartFileSystem;
    }
    public function loadFilePathToJson(string $filePath): array
    {
        $this->fileSystemGuard->ensureFileExists($filePath, __METHOD__);
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return Json::decode($fileContent, Json::FORCE_ARRAY);
    }
    public function writeJsonToFilePath(array $jsonArray, string $filePath): void
    {
        $jsonContent = Json::encode($jsonArray, Json::PRETTY) . PHP_EOL;
        $this->smartFileSystem->dumpFile($filePath, $jsonContent);
    }
    public function mergeArrayToJsonFile(string $filePath, array $newJsonArray): void
    {
        $jsonArray = $this->loadFilePathToJson($filePath);
        $newComposerJsonArray = Arrays::mergeTree($jsonArray, $newJsonArray);
        $this->writeJsonToFilePath($newComposerJsonArray, $filePath);
    }
}
