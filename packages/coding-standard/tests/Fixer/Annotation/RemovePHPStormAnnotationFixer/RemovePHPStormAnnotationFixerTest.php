<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\RemovePHPStormAnnotationFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Annotation\ValueObject\RemovePHPStormAnnotationFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\ValueObject\StaticFixtureFinder;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class RemovePHPStormAnnotationFixerTest extends AbstractCheckerTestCase
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
        return RemovePHPStormAnnotationFixer::class;
    }
}
