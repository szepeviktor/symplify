<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInIfRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\ForbiddenMethodOrStaticCallInIfRule;

final class ForbiddenMethodOrStaticCallInIfRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/WithoutMethodCall.php', []];
        yield [__DIR__ . '/Fixture/WithMethodCallWithoutParameter.php', []];
        yield [__DIR__ . '/Fixture/WithMethodCallWithParameterFromThis.php', []];
        yield [__DIR__ . '/Fixture/SkipMethodCallWithBooleanReturn.php', []];
        yield [
            __DIR__ . '/Fixture/WithMethodCallWithParameterNotFromThis.php',
            [[ForbiddenMethodOrStaticCallInIfRule::ERROR_MESSAGE, 17],
                [ForbiddenMethodOrStaticCallInIfRule::ERROR_MESSAGE, 19], ],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMethodOrStaticCallInIfRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
