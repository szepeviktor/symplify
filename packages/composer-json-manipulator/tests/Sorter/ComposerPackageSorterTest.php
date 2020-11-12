<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Tests\Sorter;

use Iterator;
use Symplify\ComposerJsonManipulator\Sorter\ValueObject\ComposerPackageSorter;
use Symplify\ComposerJsonManipulator\Tests\HttpKernel\ValueObject\ComposerJsonManipulatorKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class ComposerPackageSorterTest extends AbstractKernelTestCase
{
    /**
     * @var \Symplify\ComposerJsonManipulator\Sorter\ValueObject\ComposerPackageSorter
     */
    private $composerPackageSorter;

    protected function setUp(): void
    {
        $this->bootKernel(ComposerJsonManipulatorKernel::class);

        $this->composerPackageSorter = self::$container->get(ComposerPackageSorter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(array $packages, array $expectedSortedPackages): void
    {
        $sortedPackages = $this->composerPackageSorter->sortPackages($packages);
        $this->assertSame($expectedSortedPackages, $sortedPackages);
    }

    public function provideData(): Iterator
    {
        yield [
            [
                'symfony/console' => '^5.2',
                'php' => '^8.0',
                'ext-json' => '*',
            ],
            [
                'php' => '^8.0',
                'ext-json' => '*',
                'symfony/console' => '^5.2',
            ],
        ];
    }
}
