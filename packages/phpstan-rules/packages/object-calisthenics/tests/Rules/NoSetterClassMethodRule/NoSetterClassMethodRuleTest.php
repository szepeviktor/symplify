<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoSetterClassMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\ValueObject\NoSetterClassMethodRule;

final class NoSetterClassMethodRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(NoSetterClassMethodRule::ERROR_MESSAGE, 'setName');
        yield [__DIR__ . '/Fixture/SetterMethod.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/AllowedClass.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoSetterClassMethodRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
