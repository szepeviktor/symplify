<?php

declare(strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ValueObject;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowShortArraySyntaxSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\DisallowMultipleStatementsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowSpaceIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff;
use PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\NamespaceDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces\UseDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\LowercaseClassKeywordsSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\FunctionDeclarationArgumentSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\DoubleQuoteUsageSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\Basic\Psr4Fixer;
use PhpCsFixer\Fixer\Casing\ConstantCaseFixer;
use PhpCsFixer\Fixer\Casing\LowercaseConstantsFixer;
use PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\Comment\HashToSlashCommentFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\ControlStructure\IncludeFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Fixer\Operator\PreIncrementFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer;
use PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\Whitespace\LineEndingFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraConsecutiveBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff;
use SlevomatCodingStandard\Sniffs\Classes\ClassConstantVisibilitySniff;
use SlevomatCodingStandard\Sniffs\Commenting\ForbiddenAnnotationsSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff as SlevomatAssignmentInConditionSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\LanguageConstructWithParenthesesSniff;
use SlevomatCodingStandard\Sniffs\Files\TypeNameMatchesFileNameSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\DisallowGroupUseSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\MultipleUsesPerLineSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UnusedUsesSniff;
use SlevomatCodingStandard\Sniffs\Operators\DisallowEqualOperatorsSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\LongTypeHintsSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSpacingSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSpacingSniff;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
final class RemoveMutualCheckersCompilerPass implements CompilerPassInterface
{
    /**
     * List of checkers with the same functionality.
     * If found, only the first one is used.
     *
     * @var string[][]
     */
    private const DUPLICATED_CHECKER_GROUPS = [
        [IndentationTypeFixer::class, DisallowTabIndentSniff::class],
        [IndentationTypeFixer::class, DisallowSpaceIndentSniff::class],
        [StrictComparisonFixer::class, DisallowEqualOperatorsSniff::class],
        [VisibilityRequiredFixer::class, ClassConstantVisibilitySniff::class],
        [ArraySyntaxFixer::class, DisallowShortArraySyntaxSniff::class],
        [ArraySyntaxFixer::class, DisallowLongArraySyntaxSniff::class],
        [LowercaseKeywordsFixer::class, LowercaseClassKeywordsSniff::class],
        [LowercaseKeywordsFixer::class, LowerCaseKeywordSniff::class],
        [SingleImportPerStatementFixer::class, UseDeclarationSniff::class],
        [SingleImportPerStatementFixer::class, DisallowGroupUseSniff::class],
        [SingleImportPerStatementFixer::class, MultipleUsesPerLineSniff::class],
        [PhpdocScalarFixer::class, LongTypeHintsSniff::class],
        [OrderedImportsFixer::class, AlphabeticallySortedUsesSniff::class],
        [NoUnusedImportsFixer::class, UnusedUsesSniff::class],
        [TrailingCommaInMultilineArrayFixer::class, TrailingArrayCommaSniff::class],
        [NoUnneededControlParenthesesFixer::class, LanguageConstructWithParenthesesSniff::class],
        [Psr4Fixer::class, TypeNameMatchesFileNameSniff::class],
        [ReturnTypeDeclarationFixer::class, ReturnTypeHintSpacingSniff::class],
        [FunctionTypehintSpaceFixer::class, ParameterTypeHintSpacingSniff::class],
        [FunctionTypehintSpaceFixer::class, FunctionDeclarationArgumentSpacingSniff::class],
        [GeneralPhpdocAnnotationRemoveFixer::class, ForbiddenAnnotationsSniff::class],
        [NoExtraConsecutiveBlankLinesFixer::class, SuperfluousWhitespaceSniff::class],
        [NoExtraBlankLinesFixer::class, SuperfluousWhitespaceSniff::class],
        [IncludeFixer::class, LanguageConstructSpacingSniff::class],
        [\PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff::class, \SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff::class],
        [SingleQuoteFixer::class, DoubleQuoteUsageSniff::class],
        // PSR2
        [BracesFixer::class, ScopeClosingBraceSniff::class],
        [BlankLineAfterNamespaceFixer::class, NamespaceDeclarationSniff::class],
        [SingleLineAfterImportsFixer::class, DisallowMultipleStatementsSniff::class],
        [LineEndingFixer::class, LineEndingsSniff::class],
        [ConstantCaseFixer::class, LowerCaseConstantSniff::class],
        [LowercaseConstantsFixer::class, LowerCaseConstantSniff::class],
        [LowercaseKeywordsFixer::class, LowerCaseKeywordSniff::class],
        [SingleBlankLineAtEofFixer::class, EndFileNewlineSniff::class],
        [BracesFixer::class, ScopeIndentSniff::class],
        [BracesFixer::class, ScopeClosingBraceSniff::class],
        [ClassDefinitionFixer::class, ClassDeclarationSniff::class],
        [NoClosingTagFixer::class, ClosingTagSniff::class],
        [SingleClassElementPerStatementFixer::class, PropertyDeclarationSniff::class],
        // Aliased deprecated fixers
        [NoExtraBlankLinesFixer::class, NoExtraConsecutiveBlankLinesFixer::class],
        [ClassAttributesSeparationFixer::class, MethodSeparationFixer::class],
        [IncrementStyleFixer::class, PreIncrementFixer::class],
        [SingleLineCommentStyleFixer::class, HashToSlashCommentFixer::class],
    ];
    public function process(ContainerBuilder $containerBuilder): void
    {
        $checkersToRemove = $this->resolveCheckersToRemove($containerBuilder->getServiceIds());
        $definitions = $containerBuilder->getDefinitions();
        foreach ($definitions as $id => $definition) {
            if (in_array($definition->getClass(), $checkersToRemove, true)) {
                $containerBuilder->removeDefinition($id);
            }
        }
    }
    /**
     * @param string[] $checkers
     * @return string[]
     */
    private function resolveCheckersToRemove(array $checkers): array
    {
        $checkers = (array) array_flip($checkers);
        $checkersToRemove = [];
        foreach (self::DUPLICATED_CHECKER_GROUPS as $matchingCheckerGroup) {
            if (!$this->isMatch($checkers, $matchingCheckerGroup)) {
                continue;
            }
            array_shift($matchingCheckerGroup);
            foreach ($matchingCheckerGroup as $checkerToRemove) {
                $checkersToRemove[] = $checkerToRemove;
            }
        }
        return $checkersToRemove;
    }
    /**
     * @param string[] $checkers
     * @param string[] $matchingCheckerGroup
     */
    private function isMatch(array $checkers, array $matchingCheckerGroup): bool
    {
        $matchingCheckerGroupKeys = array_flip($matchingCheckerGroup);
        return count(array_intersect_key($matchingCheckerGroupKeys, $checkers)) === count($matchingCheckerGroup);
    }
}
