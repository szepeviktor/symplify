<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Strict\BlankLineAfterStrictTypesFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Strict\ValueObject\BlankLineAfterStrictTypesFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\ValueObject\StaticFixtureFinder;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class BlankLineAfterStrictTypesFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testFix(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture');
    }

    protected function getCheckerClass(): string
    {
        return BlankLineAfterStrictTypesFixer::class;
    }
}
