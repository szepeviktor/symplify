<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Commenting\ValueObject\ParamReturnAndVarTagMalformsFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\ValueObject\StaticFixtureFinder;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

/**
 * @requires PHP >= 7.4
 * @mimic https://github.com/rectorphp/rector/pull/807/files
 */
final class Php74Test extends AbstractCheckerTestCase
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
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/FixturePhp74');
    }

    protected function getCheckerClass(): string
    {
        return ParamReturnAndVarTagMalformsFixer::class;
    }
}
