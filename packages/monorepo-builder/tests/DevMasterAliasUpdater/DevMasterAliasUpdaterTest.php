<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\DevMasterAliasUpdater;

use Symplify\MonorepoBuilder\ValueObject\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\HttpKernel\ValueObject\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;

final class DevMasterAliasUpdaterTest extends AbstractKernelTestCase
{
    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\DevMasterAliasUpdater
     */
    private $devMasterAliasUpdater;

    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileSystem
     */
    private $smartFileSystem;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->devMasterAliasUpdater = self::$container->get(DevMasterAliasUpdater::class);
        $this->smartFileSystem = self::$container->get(SmartFileSystem::class);
    }

    protected function tearDown(): void
    {
        $this->smartFileSystem->copy(__DIR__ . '/Source/backup-first.json', __DIR__ . '/Source/first.json');
    }

    public function test(): void
    {
        $fileInfos = [new SmartFileInfo(__DIR__ . '/Source/first.json')];

        $this->devMasterAliasUpdater->updateFileInfosWithAlias($fileInfos, '4.5-dev');

        $this->assertFileEquals(__DIR__ . '/Source/expected-first.json', __DIR__ . '/Source/first.json');
    }
}
