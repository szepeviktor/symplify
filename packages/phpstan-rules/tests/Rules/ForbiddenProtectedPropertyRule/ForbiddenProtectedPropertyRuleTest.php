<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenProtectedPropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\ForbiddenProtectedPropertyRule;

final class ForbiddenProtectedPropertyRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/HasNonProtectedPropertyAndConstant.php', []];
        yield [__DIR__ . '/Fixture/AbstractClassWithConstructorInjection.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractClassWithConstructorSetValues.php', []];
        yield [__DIR__ . '/Fixture/AbstractClassWithAutowireInjection.php', []];
        yield [__DIR__ . '/Fixture/AbstractClassWithTestCaseSetUp.php', []];
        yield [__DIR__ . '/Fixture/AbstractAnyTestCase.php', []];
        yield [__DIR__ . '/Fixture/HasProtectedPropertyAndConstant.php',
            [
                [ForbiddenProtectedPropertyRule::ERROR_MESSAGE, 11],
                [ForbiddenProtectedPropertyRule::ERROR_MESSAGE, 15],
            ],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenProtectedPropertyRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
