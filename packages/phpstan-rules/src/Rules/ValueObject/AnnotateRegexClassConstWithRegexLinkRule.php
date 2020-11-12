<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\AnnotateRegexClassConstWithRegexLinkRule\AnnotateRegexClassConstWithRegexLinkRuleTest
 */
final class AnnotateRegexClassConstWithRegexLinkRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Add regex101.com link to that shows the regex in practise, so it will be easier to maintain in case of bug/extension in the future';
    /**
     * @var string
     * @see https://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
     */
    private const ALL_MODIFIERS = 'imsxeADSUXJu';
    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [\PhpParser\Node\Stmt\ClassConst::class];
    }
    /**
     * @param ClassConst $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (count((array) $node->consts) !== 1) {
            return [];
        }
        $onlyConst = $node->consts[0];
        if (!$onlyConst->value instanceof String_) {
            return [];
        }
        $constantName = (string) $onlyConst->name;
        if (!$this->isRegexPatternConstantName($constantName)) {
            return [];
        }
        $stringValue = $onlyConst->value->value;
        if (!$this->isNonSingleCharRegexPattern($stringValue)) {
            return [];
        }
        // is regex patern
        if ($this->hasDocBlockWithRegexLink($node)) {
            return [];
        }
        return [self::ERROR_MESSAGE];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    private const COMPLICATED_REGEX = '#some_complicated_stu|ff#';
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
/**
 * @see https://regex101.com/r/SZr0X5/12
 */
class SomeClass
{
    private const COMPLICATED_REGEX = '#some_complicated_stu|ff#';
}
CODE_SAMPLE
)]);
    }
    private function isNonSingleCharRegexPattern(string $value): bool
    {
        // skip 1-char regexs
        if (Strings::length($value) < 4) {
            return false;
        }
        $firstChar = $value[0];
        if (ctype_alpha($firstChar)) {
            return false;
        }
        $patternWithoutModifiers = rtrim($value, self::ALL_MODIFIERS);
        if (Strings::length($patternWithoutModifiers) < 1) {
            return false;
        }
        $lastChar = Strings::substring($patternWithoutModifiers, -1, 1);
        if ($firstChar !== $lastChar) {
            return false;
        }
        // this is probably a regex
        return true;
    }
    private function hasDocBlockWithRegexLink(Node $node): bool
    {
        if ($node->getDocComment() === null) {
            return false;
        }
        $docCommentText = $node->getDocComment()->getText();
        return Strings::contains($docCommentText, '@see https://regex101.com/r');
    }
    private function isRegexPatternConstantName(string $constantName): bool
    {
        return (bool) Strings::endsWith($constantName, '_REGEX');
    }
}
