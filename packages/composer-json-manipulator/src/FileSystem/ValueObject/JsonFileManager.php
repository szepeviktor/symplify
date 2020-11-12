<?php

declare(strict_types=1);
namespace Symplify\ComposerJsonManipulator\FileSystem\ValueObject;

use Nette\Utils\Json;
use Symplify\ComposerJsonManipulator\Json\ValueObject\JsonCleaner;
use Symplify\ComposerJsonManipulator\Json\ValueObject\JsonInliner;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\PackageBuilder\Configuration\ValueObject\StaticEolConfiguration;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;
/**
 * @see \Symplify\MonorepoBuilder\Tests\FileSystem\JsonFileManager\JsonFileManagerTest
 */
final class JsonFileManager
{
    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var \Symplify\ComposerJsonManipulator\Json\ValueObject\JsonCleaner
     */
    private $jsonCleaner;
    /**
     * @var \Symplify\ComposerJsonManipulator\Json\ValueObject\JsonInliner
     */
    private $jsonInliner;
    public function __construct(SmartFileSystem $smartFileSystem, JsonCleaner $jsonCleaner, JsonInliner $jsonInliner)
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->jsonCleaner = $jsonCleaner;
        $this->jsonInliner = $jsonInliner;
    }
    /**
     * @return mixed[]
     */
    public function loadFromFileInfo(SmartFileInfo $smartFileInfo): array
    {
        return Json::decode($smartFileInfo->getContents(), Json::FORCE_ARRAY);
    }
    /**
     * @return mixed[]
     */
    public function loadFromFilePath(string $filePath): array
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return Json::decode($fileContent, Json::FORCE_ARRAY);
    }
    /**
     * @param mixed[] $json
     */
    public function printJsonToFileInfo(array $json, SmartFileInfo $smartFileInfo): string
    {
        $jsonString = $this->encodeJsonToFileContent($json);
        $this->smartFileSystem->dumpFile($smartFileInfo->getPathname(), $jsonString);
        return $jsonString;
    }
    public function printComposerJsonToFilePath(ComposerJson $composerJson, string $filePath): string
    {
        $jsonString = $this->encodeJsonToFileContent($composerJson->getJsonArray());
        $this->smartFileSystem->dumpFile($filePath, $jsonString);
        return $jsonString;
    }
    /**
     * @param mixed[] $json
     */
    public function encodeJsonToFileContent(array $json): string
    {
        // Empty arrays may lead to bad encoding since we can't be sure whether they need to be arrays or objects.
        $json = $this->jsonCleaner->removeEmptyKeysFromJsonArray($json);
        $jsonContent = Json::encode($json, Json::PRETTY) . StaticEolConfiguration::getEolChar();
        return $this->jsonInliner->inlineSections($jsonContent);
    }
}
