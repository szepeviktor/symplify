<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\InterdependencyUpdater;

use Symplify\MonorepoBuilder\ValueObject\DependencyUpdater;
use Symplify\MonorepoBuilder\HttpKernel\ValueObject\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;

final class InterdependencyUpdaterTest extends AbstractKernelTestCase
{
    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\DependencyUpdater
     */
    private $dependencyUpdater;

    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileSystem
     */
    private $smartFileSystem;

    protected function setUp(): void
    {
        $this->bootKernel(MonorepoBuilderKernel::class);

        $this->dependencyUpdater = self::$container->get(DependencyUpdater::class);
        $this->smartFileSystem = self::$container->get(SmartFileSystem::class);
    }

    protected function tearDown(): void
    {
        $this->smartFileSystem->copy(__DIR__ . '/Source/backup-first.json', __DIR__ . '/Source/first.json', true);
    }

    public function testVendor(): void
    {
        $this->dependencyUpdater->updateFileInfosWithVendorAndVersion(
            [new SmartFileInfo(__DIR__ . '/Source/first.json')],
            'symplify',
            '^5.0'
        );

        $this->assertFileEquals(__DIR__ . '/Source/expected-first-vendor.json', __DIR__ . '/Source/first.json');
    }

    public function testPackages(): void
    {
        $this->dependencyUpdater->updateFileInfosWithPackagesAndVersion(
            [new SmartFileInfo(__DIR__ . '/Source/first.json')],
            ['symplify/coding-standard'],
            '^6.0'
        );

        $this->assertFileEquals(__DIR__ . '/Source/expected-first-packages.json', __DIR__ . '/Source/first.json');
    }
}
