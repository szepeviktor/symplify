<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\CheckConstantExpressionDefinedInConstructOrSetupRule;

final class CheckConstantExpressionDefinedInConstructOrSetupRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipInForeachAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipInConstructOrSetUpMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipPropertySetter.php', []];

        yield [__DIR__ . '/Fixture/InsideOtherMethodInsideIf.php', []];
        yield [__DIR__ . '/Fixture/AllowInsideOtherMethodUsedAfterDefinition.php', []];
        yield [__DIR__ . '/Fixture/AllowMagicConstantWithConcatMethodCall.php', []];

        yield [__DIR__ . '/Fixture/SkipFuncCallInConcat.php', []];

        yield [__DIR__ . '/Fixture/GetCwdFuncCallInConcat.php', [
            [CheckConstantExpressionDefinedInConstructOrSetupRule::ERROR_MESSAGE, 11],
        ]];

        yield [
            __DIR__ . '/Fixture/StringIntConcat.php',
            [
                [CheckConstantExpressionDefinedInConstructOrSetupRule::ERROR_MESSAGE, 11],
                [CheckConstantExpressionDefinedInConstructOrSetupRule::ERROR_MESSAGE, 13],
            ],
        ];

        yield [
            __DIR__ . '/Fixture/DisallowMagicConstantWithConcatString.php',
            [[CheckConstantExpressionDefinedInConstructOrSetupRule::ERROR_MESSAGE, 11]],
        ];

        yield [
            __DIR__ . '/Fixture/DisallowInsideOtherMethodNextDeadCode.php',
            [[CheckConstantExpressionDefinedInConstructOrSetupRule::ERROR_MESSAGE, 11]],
        ];

        yield [
            __DIR__ . '/Fixture/Multiplex.php',
            [
                [CheckConstantExpressionDefinedInConstructOrSetupRule::ERROR_MESSAGE, 11],
                [CheckConstantExpressionDefinedInConstructOrSetupRule::ERROR_MESSAGE, 13],
            ],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckConstantExpressionDefinedInConstructOrSetupRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
