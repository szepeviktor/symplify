<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogDumper;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ValueObject\ChangelogDumper;
use Symplify\ChangelogLinker\ValueObject\ChangelogFormatter;
use Symplify\ChangelogLinker\Git\ValueObject\GitCommitDateTagResolver;
use Symplify\ChangelogLinker\ValueObject\ChangeTree\Change;

final class ChangelogDumperTest extends TestCase
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
        $this->changelogDumper = new ChangelogDumper(new GitCommitDateTagResolver(), new ChangelogFormatter());

        $this->changes = [new Change('[SomePackage] Message', 'Added', 'SomePackage', 'Message', 'Unreleased')];
    }

    public function testReportChanges(): void
    {
        $content = $this->changelogDumper->reportChangesWithHeadlines($this->changes, false, false, 'packages');

        $this->assertStringEqualsFile(__DIR__ . '/ChangelogDumperSource/expected1.md', $content);
    }

    /**
     * @dataProvider provideDataForReportBothWithPriority()
     */
    public function testReportBothWithPriority(
        bool $withCategories,
        bool $withPackages,
        string $priority,
        string $expectedOutputFile
    ): void {
        $content = $this->changelogDumper->reportChangesWithHeadlines(
            $this->changes,
            $withCategories,
            $withPackages,
            $priority
        );

        $this->assertStringEqualsFile($expectedOutputFile, $content);
    }

    public function provideDataForReportBothWithPriority(): Iterator
    {
        yield [true, false, 'categories', __DIR__ . '/ChangelogDumperSource/expected2.md'];
        yield [false, true, 'packages', __DIR__ . '/ChangelogDumperSource/expected3.md'];
        yield [true, true, 'packages', __DIR__ . '/ChangelogDumperSource/expected4.md'];
        yield [true, true, 'categories', __DIR__ . '/ChangelogDumperSource/expected5.md'];
    }
}
