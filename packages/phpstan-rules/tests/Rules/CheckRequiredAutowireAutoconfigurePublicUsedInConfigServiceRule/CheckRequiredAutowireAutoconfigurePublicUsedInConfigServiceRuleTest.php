<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule;

final class CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/ConfigParameter.php', []];
        yield [__DIR__ . '/Fixture/ConfigServiceHasAutowireAutoConfigurePublicMethodCall.php', []];
        yield [
            __DIR__ . '/Fixture/ConfigServiceMissingMethodCall.php',
            [[CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule::ERROR_MESSAGE, 9]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckRequiredAutowireAutoconfigurePublicUsedInConfigServiceRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
