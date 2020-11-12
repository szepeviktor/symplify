<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\FileSystem\JsonFileManager;

use Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager;
use Symplify\MonorepoBuilder\HttpKernel\ValueObject\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;

final class JsonFileManagerTest extends AbstractKernelTestCase
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
        $this->bootKernelWithConfigs(MonorepoBuilderKernel::class, [
            __DIR__ . '/config/inlined_section_config.php',
        ]);

        $this->jsonFileManager = self::$container->get(JsonFileManager::class);
        $this->smartFileSystem = self::$container->get(SmartFileSystem::class);
    }

    protected function tearDown(): void
    {
        $this->smartFileSystem->remove(__DIR__ . '/Source/second.json');
        $this->smartFileSystem->remove(__DIR__ . '/Source/third.json');
    }

    public function testLoad(): void
    {
        $expectedJson = [
            'key' => 'value',
        ];

        $this->assertSame($expectedJson, $this->jsonFileManager->loadFromFilePath(__DIR__ . '/Source/first.json'));

        $this->assertSame(
            $expectedJson,
            $this->jsonFileManager->loadFromFileInfo(new SmartFileInfo(__DIR__ . '/Source/first.json'))
        );
    }

    public function testEncodeArrayToString(): void
    {
        $jsonContent = $this->jsonFileManager->encodeJsonToFileContent([
            'another_key' => 'another_value',
        ]);
        $this->assertStringEqualsFile(__DIR__ . '/Source/expected-second.json', $jsonContent);
    }

    public function testSaveWithInlinedSections(): void
    {
        $fileContent = $this->jsonFileManager->encodeJsonToFileContent([
            'inline_section' => [1, 2, 3],
            'normal_section' => [1, 2, 3],
        ]);

        $this->assertStringEqualsFile(__DIR__ . '/Source/expected-inlined.json', $fileContent);
    }
}
