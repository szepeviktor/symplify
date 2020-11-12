<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Finder\PackageComposerFinder;

use Symplify\MonorepoBuilder\Finder\ValueObject\PackageComposerFinder;
use Symplify\MonorepoBuilder\HttpKernel\ValueObject\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class PackageComposerFinderTest extends AbstractKernelTestCase
{
    /**
     * @var \Symplify\MonorepoBuilder\Finder\ValueObject\PackageComposerFinder
     */
    private $packageComposerFinder;

    protected function setUp(): void
    {
        self::bootKernelWithConfigs(MonorepoBuilderKernel::class, [__DIR__ . '/Source/source_config.php']);
        $this->packageComposerFinder = self::$container->get(PackageComposerFinder::class);
    }

    public function test(): void
    {
        $this->assertCount(2, $this->packageComposerFinder->getPackageComposerFiles());
    }
}
