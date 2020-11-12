<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Annotation\ValueObject\DoctrineAnnotationNewlineInNestedAnnotationFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\ValueObject\StaticFixtureFinder;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class DoctrineAnnotationNewlineInNestedAnnotationFixerTest extends AbstractCheckerTestCase
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
        return DoctrineAnnotationNewlineInNestedAnnotationFixer::class;
    }
}
