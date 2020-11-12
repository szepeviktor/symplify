<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Testing\Tests\ComposerJson;

use Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager;
use Symplify\MonorepoBuilder\HttpKernel\ValueObject\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\Testing\ComposerJson\ValueObject\ComposerJsonSymlinker;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class ComposerJsonSymlinkerTest extends AbstractKernelTestCase
{
    /**
     * @var \Symplify\MonorepoBuilder\Testing\ComposerJson\ValueObject\ComposerJsonSymlinker
     */
    private $composerJsonSymlinker;

    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager
     */
    private $jsonFileManager;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->jsonFileManager = self::$container->get(JsonFileManager::class);
        $this->composerJsonSymlinker = self::$container->get(ComposerJsonSymlinker::class);
    }

    public function testItCanAppendPathRepository(): void
    {
        $mainComposerJson = new SmartFileInfo(__DIR__ . '/composer.json');
        $packageFileInfo = new SmartFileInfo(__DIR__ . '/packages/package-one/composer.json');

        $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageFileInfo);

        $packageComposerJson = $this->composerJsonSymlinker->decoratePackageComposerJsonWithPackageSymlinks(
            $packageComposerJson,
            ['example/package-two'],
            $mainComposerJson
        );

        $this->assertSame([
            'name' => 'example/package-one',
            'repositories' => [
                [
                    'type' => 'path',
                    'url' => '../../packages/package-two',
                    'options' => [
                        'symlink' => false,
                    ],
                    //                    'canonical' => true,
                ],
                [
                    'type' => 'composer',
                    'url' => 'https://repo.packagist.com/acme-companies/',
                ],
                [
                    'packagist.org' => false,
                ],
            ],
        ], $packageComposerJson);
    }

    public function testItCanAddPathRepository(): void
    {
        $mainComposerJson = new SmartFileInfo(__DIR__ . '/composer.json');
        $packageFileInfo = new SmartFileInfo(__DIR__ . '/packages/package-two/composer.json');

        $packageComposerJson = $this->jsonFileManager->loadFromFileInfo($packageFileInfo);

        $packageComposerJson = $this->composerJsonSymlinker->decoratePackageComposerJsonWithPackageSymlinks(
            $packageComposerJson,
            ['example/package-one'],
            $mainComposerJson
        );

        $this->assertSame([
            'name' => 'example/package-two',
            'repositories' => [
                [
                    'type' => 'path',
                    'url' => '../../packages/package-one',
                    'options' => [
                        'symlink' => false,
                    ],
                ],
            ],
        ], $packageComposerJson);
    }
}
