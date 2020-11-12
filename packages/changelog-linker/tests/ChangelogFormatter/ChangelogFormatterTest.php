<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogFormatter;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\ChangelogLinker\ValueObject\ChangelogFormatter;
use Symplify\EasyTesting\DataProvider\ValueObject\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class ChangelogFormatterTest extends TestCase
{
    /**
     * @var \Symplify\ChangelogLinker\ValueObject\ChangelogFormatter
     */
    private $changelogFormatter;

    protected function setUp(): void
    {
        $this->changelogFormatter = new ChangelogFormatter();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fixtureFileInfo);

        $output = $this->changelogFormatter->format($inputAndExpected->getInput());
        $this->assertSame($inputAndExpected->getExpected(), $output, $fixtureFileInfo->getRelativeFilePathFromCwd());
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Source', '*.txt');
    }
}
