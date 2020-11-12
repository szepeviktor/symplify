<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequireThisOnParentMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\RequireThisOnParentMethodCallRule;

final class RequireThisOnParentMethodCallRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/CallParentMethodStaticallySameMethod.php', []];
        yield [__DIR__ . '/Fixture/CallParentMethodStaticallyWhenMethodOverriden.php', []];
        yield [
            __DIR__ . '/Fixture/CallParentMethodStatically.php',
            [[RequireThisOnParentMethodCallRule::ERROR_MESSAGE, 11], [
                RequireThisOnParentMethodCallRule::ERROR_MESSAGE,
                12,
            ]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireThisOnParentMethodCallRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
