<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\NoParentMethodCallOnEmptyStatementInParentMethodRule;

final class NoParentMethodCallOnEmptyStatementInParentMethodRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipException.php', []];
        yield [__DIR__ . '/Fixture/NotCallParentMethod.php', []];
        yield [__DIR__ . '/Fixture/CallParentMethodWithStatement.php', []];

        yield [
            __DIR__ . '/Fixture/CallParentMethod.php',
            [[NoParentMethodCallOnEmptyStatementInParentMethodRule::ERROR_MESSAGE, 11]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoParentMethodCallOnEmptyStatementInParentMethodRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
