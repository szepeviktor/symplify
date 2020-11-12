<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Debug\CommentedOutCode;

use Iterator;
use Symplify\CodingStandard\Sniffs\Debug\ValueObject\CommentedOutCodeSniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\ValueObject\StaticFixtureFinder;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class CommentedOutCodeSniffTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture');
    }

    protected function getCheckerClass(): string
    {
        return CommentedOutCodeSniff::class;
    }
}
