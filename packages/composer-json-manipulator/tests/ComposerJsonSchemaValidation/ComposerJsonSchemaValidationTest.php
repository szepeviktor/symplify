<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Tests\ComposerJsonSchemaValidation;

use Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager;
use Symplify\ComposerJsonManipulator\Tests\HttpKernel\ValueObject\ComposerJsonManipulatorKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;

final class ComposerJsonSchemaValidationTest extends AbstractKernelTestCase
{
    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileSystem
     */
    private $smartFileSystem;

    protected function setUp(): void
    {
        $this->bootKernel(ComposerJsonManipulatorKernel::class);

        $this->jsonFileManager = self::$container->get(JsonFileManager::class);
        $this->smartFileSystem = new SmartFileSystem();
    }

    public function testCheckEmptyKeysAreRemoved(): void
    {
        $sourceJsonPath = __DIR__ . '/Source/symfony-website_skeleton-composer.json';
        $targetJsonPath = sys_get_temp_dir() . '/composer_json_manipulator_test_schema_validation.json';

        $sourceJson = $this->jsonFileManager->loadFromFilePath($sourceJsonPath);
        $this->smartFileSystem->dumpFile($targetJsonPath, $this->jsonFileManager->encodeJsonToFileContent($sourceJson));

        $sourceJson = $this->jsonFileManager->loadFromFilePath($sourceJsonPath);
        $targetJson = $this->jsonFileManager->loadFromFilePath($targetJsonPath);

        /*
         * Check empty keys are present in "source" but not in "target"
         */
        $this->assertArrayHasKey('require-dev', $sourceJson);
        $this->assertArrayHasKey('auto-scripts', $sourceJson['scripts']);
        $this->assertArrayNotHasKey('require-dev', $targetJson);
        $this->assertArrayNotHasKey('auto-scripts', $targetJson['scripts']);
    }
}
