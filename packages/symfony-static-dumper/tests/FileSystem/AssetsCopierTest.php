<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\FileSystem;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;
use Symplify\SymfonyStaticDumper\FileSystem\ValueObject\AssetsCopier;
use Symplify\SymfonyStaticDumper\Tests\TestProject\HttpKernel\ValueObject\TestSymfonyStaticDumperKernel;

final class AssetsCopierTest extends AbstractKernelTestCase
{
    /**
     * @var \Symplify\SymfonyStaticDumper\FileSystem\ValueObject\AssetsCopier
     */
    private $assetsCopier;

    protected function setUp(): void
    {
        $this->bootKernel(TestSymfonyStaticDumperKernel::class);

        $this->assetsCopier = self::$container->get(AssetsCopier::class);
    }

    protected function tearDown(): void
    {
        $smartFileSystem = new SmartFileSystem();
        $smartFileSystem->remove(__DIR__ . '/temp');
    }

    public function test(): void
    {
        $this->assetsCopier->copyAssets(__DIR__ . '/Fixture', __DIR__ . '/temp');

        $this->assertFileExists(__DIR__ . '/temp/css/style.css');

        // PHPUnit 9 + 10 compatible
        if (method_exists($this, 'assertFileDoesNotExist')) {
            $this->assertFileDoesNotExist(__DIR__ . '/temp/file.php');
        } else {
            $this->assertFileNotExists(__DIR__ . '/temp/file.php');
        }
    }
}
