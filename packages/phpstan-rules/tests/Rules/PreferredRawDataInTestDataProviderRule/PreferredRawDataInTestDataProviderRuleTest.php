<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredRawDataInTestDataProviderRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\PreferredRawDataInTestDataProviderRule;

final class PreferredRawDataInTestDataProviderRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/NoDataProviderTest.php', []];
        yield [__DIR__ . '/Fixture/UseRawDataForTestDataProviderTest.php', []];
        yield [
            __DIR__ . '/Fixture/UseDataFromSetupInTestDataProviderTest.php',
            [[PreferredRawDataInTestDataProviderRule::ERROR_MESSAGE, 24]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PreferredRawDataInTestDataProviderRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
