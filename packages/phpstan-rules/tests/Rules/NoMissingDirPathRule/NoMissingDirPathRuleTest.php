<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoMissingDirPathRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\NoMissingDirPathRule;

final class NoMissingDirPathRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $message = sprintf(NoMissingDirPathRule::ERROR_MESSAGE, '/not_here.php');
        yield [__DIR__ . '/Fixture/FileMissing.php', [[$message, 11]]];

        yield [__DIR__ . '/Fixture/SkipExistingFile.php', []];
        yield [__DIR__ . '/Fixture/SkipVendorAutoload.php', []];
        yield [__DIR__ . '/Fixture/SkipAssertMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipFnMatch.php', []];
        yield [__DIR__ . '/Fixture/SkipFileExistsFuncCall.php', []];
        yield [__DIR__ . '/Fixture/SkipFileExistsFuncCallOneLayerAbove.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoMissingDirPathRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
