<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\AnnotateRegexClassConstWithRegexLinkRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ValueObject\AnnotateRegexClassConstWithRegexLinkRule;

final class AnnotateRegexClassConstWithRegexLinkRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [
            __DIR__ . '/Fixture/ClassConstMissingLink.php',
            [[AnnotateRegexClassConstWithRegexLinkRule::ERROR_MESSAGE, 12]],
        ];

        yield [__DIR__ . '/Fixture/SkipShort.php', []];
        yield [__DIR__ . '/Fixture/SkipWithLink.php', []];
        yield [__DIR__ . '/Fixture/SkipAlphabet.php', []];
        yield [__DIR__ . '/Fixture/SkipPlaceholder.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            AnnotateRegexClassConstWithRegexLinkRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
