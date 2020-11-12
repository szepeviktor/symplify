<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenComplexArrayConfigInSetRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\ForbiddenComplexArrayConfigInSetRule;

final class ForbiddenComplexArrayConfigInSetRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/ComplexConfig.php', [[ForbiddenComplexArrayConfigInSetRule::ERROR_MESSAGE, 15]]];

        yield [__DIR__ . '/Fixture/SkipSimpleConfig.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenComplexArrayConfigInSetRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
