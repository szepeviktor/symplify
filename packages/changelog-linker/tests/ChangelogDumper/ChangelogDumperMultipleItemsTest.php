<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogDumper;

use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ValueObject\ChangelogDumper;
use Symplify\ChangelogLinker\ValueObject\ChangelogFormatter;
use Symplify\ChangelogLinker\Git\ValueObject\GitCommitDateTagResolver;
use Symplify\ChangelogLinker\ValueObject\ChangeTree\Change;

final class ChangelogDumperMultipleItemsTest extends TestCase
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

        $this->changes = [
            new Change('[SomePackage] Message', 'Added', 'SomePackage', 'Message', 'Unreleased'),
            new Change('[AnotherPackage] Message', 'Fixed', 'AnotherPackage', 'Another Message', 'Unreleased'),
        ];
    }

    public function testReportBothWithPriority(): void
    {
        $content = $this->changelogDumper->reportChangesWithHeadlines($this->changes, true, true, 'packages');

        $this->assertStringEqualsFile(__DIR__ . '/ChangelogDumperSource/expected-multiple1.md', $content);
    }
}
