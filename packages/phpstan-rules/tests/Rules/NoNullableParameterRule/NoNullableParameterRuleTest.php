<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNullableParameterRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\NoNullableParameterRule;

final class NoNullableParameterRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(NoNullableParameterRule::ERROR_MESSAGE, 'value');
        yield [__DIR__ . '/Fixture/MethodWithNullableParam.php', [[$errorMessage, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoNullableParameterRule::class,
            __DIR__ . '/../../../config/symplify-strict-rules.neon'
        );
    }
}
