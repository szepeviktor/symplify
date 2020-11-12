<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDefaultExceptionRule;

use Iterator;
use PHPStan\Rules\Rule;
use RuntimeException;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\NoDefaultExceptionRule;

final class NoDefaultExceptionRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(NoDefaultExceptionRule::ERROR_MESSAGE, RuntimeException::class);
        yield [__DIR__ . '/Fixture/ThrowGenericException.php', [[$errorMessage, 13]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoDefaultExceptionRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
