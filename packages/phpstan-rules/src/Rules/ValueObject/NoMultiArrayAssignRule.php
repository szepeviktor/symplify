<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\Expression;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoMultiArrayAssignRule\NoMultiArrayAssignRuleTest
 */
final class NoMultiArrayAssignRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use value object over multi array assign';
    /**
     * @var Standard
     */
    private $printerStandard;
    public function __construct(\PhpParser\PrettyPrinter\Standard $printerStandard)
    {
        $this->printerStandard = $printerStandard;
    }
    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [\PhpParser\Node\Expr\Assign::class];
    }
    /**
     * @param Assign $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (!$node->var instanceof ArrayDimFetch) {
            return [];
        }
        // is previous array dim assign too? - print the exprt conteont
        $previousArrayDimFetch = $this->matchParentArrayDimFetch($node);
        if ($previousArrayDimFetch === null) {
            return [];
        }
        if (!$this->haveSameArrayDimFetchNonEmptyRoot($node->var, $previousArrayDimFetch)) {
            return [];
        }
        return [self::ERROR_MESSAGE];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        $values = [];
        $values['person']['name'] = 'Tom';
        $values['person']['surname'] = 'Dev';
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        $values = [];
        $values[] = new Person('Tom', 'Dev');
    }
}
CODE_SAMPLE
)]);
    }
    private function haveSameArrayDimFetchNonEmptyRoot(ArrayDimFetch $firstArrayDimFetch, ArrayDimFetch $secondArrayDimFetch): bool
    {
        $singleNestedFirstArrayDimFetch = $this->resolveSingleNestedArrayDimFetch($firstArrayDimFetch);
        $singleNestedSecondArrayDimFetch = $this->resolveSingleNestedArrayDimFetch($secondArrayDimFetch);
        if ($singleNestedFirstArrayDimFetch->dim === null) {
            return false;
        }
        return $this->areNodesEqual($singleNestedFirstArrayDimFetch, $singleNestedSecondArrayDimFetch);
    }
    private function resolveSingleNestedArrayDimFetch(ArrayDimFetch $arrayDimFetch): ArrayDimFetch
    {
        while ($arrayDimFetch->var instanceof ArrayDimFetch) {
            $arrayDimFetch = $arrayDimFetch->var;
        }
        return $arrayDimFetch;
    }
    private function areNodesEqual(Node $firstNode, Node $secondNode): bool
    {
        return $this->printerStandard->prettyPrint([$firstNode]) === $this->printerStandard->prettyPrint([$secondNode]);
    }
    private function matchParentArrayDimFetch(Node $node): ?ArrayDimFetch
    {
        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        if (!$parent instanceof Expression) {
            return null;
        }
        $previous = $parent->getAttribute(PHPStanAttributeKey::PREVIOUS);
        if (!$previous instanceof Expression) {
            return null;
        }
        if (!$previous->expr instanceof \PhpParser\Node\Expr\Assign) {
            return null;
        }
        $assign = $previous->expr;
        if (!$assign->var instanceof ArrayDimFetch) {
            return null;
        }
        return $assign->var;
    }
}
