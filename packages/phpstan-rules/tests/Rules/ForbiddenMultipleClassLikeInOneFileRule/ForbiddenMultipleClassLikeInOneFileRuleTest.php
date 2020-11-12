<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMultipleClassLikeInOneFileRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\ForbiddenMultipleClassLikeInOneFileRule;

final class ForbiddenMultipleClassLikeInOneFileRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/OneInterface.php', []];
        yield [__DIR__ . '/Fixture/OneClassWithAnonymousClass.php', []];
        yield [__DIR__ . '/Fixture/OneClass.php', []];
        yield [__DIR__ . '/Fixture/OneTrait.php', []];
        yield [__DIR__ . '/Fixture/NoClassLike.php', []];
        yield [
            __DIR__ . '/Fixture/MultipleClassLike.php',
            [[ForbiddenMultipleClassLikeInOneFileRule::ERROR_MESSAGE, 3]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            ForbiddenMultipleClassLikeInOneFileRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
