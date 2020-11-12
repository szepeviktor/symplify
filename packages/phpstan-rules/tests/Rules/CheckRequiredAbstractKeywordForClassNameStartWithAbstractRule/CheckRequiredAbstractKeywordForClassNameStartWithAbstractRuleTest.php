<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredAbstractKeywordForClassNameStartWithAbstractRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\CheckRequiredAbstractKeywordForClassNameStartWithAbstractRule;

final class CheckRequiredAbstractKeywordForClassNameStartWithAbstractRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/AbstractClass.php', []];
        yield [__DIR__ . '/Fixture/SomeClass.php', []];

        yield [
            __DIR__ . '/Fixture/NonAbstractClassWithAbstractPrefix.php',
            [[CheckRequiredAbstractKeywordForClassNameStartWithAbstractRule::ERROR_MESSAGE, 7]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckRequiredAbstractKeywordForClassNameStartWithAbstractRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
