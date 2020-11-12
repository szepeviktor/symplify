<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogDumper;

use Iterator;
use Symplify\ChangelogLinker\ValueObject\ChangelogDumper;
use Symplify\ChangelogLinker\HttpKernel\ValueObject\ChangelogLinkerKernel;
use Symplify\ChangelogLinker\ValueObject\ChangeTree\Change;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class WithTagsTest extends AbstractKernelTestCase
{
    /**
     * @var Change[]
     */
    private $changes = [];

    /**
     * @var \Symplify\ChangelogLinker\ValueObject\ChangelogDumper
     */
    private $changelogDumper;

    protected function setUp(): void
    {
        $this->bootKernel(ChangelogLinkerKernel::class);
        $this->changelogDumper = self::$container->get(ChangelogDumper::class);

        $this->changes = [new Change('[SomePackage] Message', 'Added', 'SomePackage', 'Message', 'v4.0.0')];
    }

    public function testReportChanges(): void
    {
        $this->markTestSkipped('Random false positives on Github Actions');

        $content = $this->changelogDumper->reportChangesWithHeadlines($this->changes, false, false, 'categories');

        $expectedFile = __DIR__ . '/WithTagsSource/expected1.md';
        $this->assertStringEqualsFile($expectedFile, $content);
    }

    /**
     * @dataProvider provideDataForReportChangesWithHeadlines()
     */
    public function testReportBothWithCategoriesPriority(
        bool $withCategories,
        bool $withPackages,
        ?string $priority,
        string $expectedOutputFile
    ): void {
        $this->markTestSkipped('Random false positives on Github Actions');

        $content = $this->changelogDumper->reportChangesWithHeadlines(
            $this->changes,
            $withCategories,
            $withPackages,
            $priority
        );

        $this->assertStringEqualsFile($expectedOutputFile, $content);
    }

    public function provideDataForReportChangesWithHeadlines(): Iterator
    {
        yield [true, false, null, __DIR__ . '/WithTagsSource/expected2.md'];
        yield [false, true, null, __DIR__ . '/WithTagsSource/expected3.md'];
    }
}
