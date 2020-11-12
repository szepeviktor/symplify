<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use PhpParser\Node;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckTraitMethodOnlyDelegateOtherClassRule\CheckTraitMethodOnlyDelegateOtherClassRuleTest
 */
final class CheckTraitMethodOnlyDelegateOtherClassRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Trait method "%s()" should not contain any logic, but only delegate to other class call';
    /**
     * @var NodeFinder
     */
    private $nodeFinder;
    public function __construct(\PhpParser\NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }
    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [\PhpParser\Node\Stmt\Trait_::class];
    }
    /**
     * @param Trait_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var ClassMethod[] $classMethods */
        $classMethods = $this->nodeFinder->findInstanceOf($node, ClassMethod::class);
        foreach ($classMethods as $classMethod) {
            $classMethodName = $classMethod->name->toString();
            if ($this->hasMethodCallFromThis($classMethod)) {
                return [sprintf(self::ERROR_MESSAGE, $classMethodName)];
            }
            if ($this->hasInstanceOfExpression($classMethod)) {
                return [sprintf(self::ERROR_MESSAGE, $classMethodName)];
            }
        }
        return [];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
trait SomeTrait
{
    public function someComplexLogic()
    {
        if (...) {
        } else {
            // ...
        }
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
trait SomeTrait
{
    public function someDelegateCall()
    {
        $this->singleDelegateCall();
    }
}
CODE_SAMPLE
)]);
    }
    private function hasMethodCallFromThis(ClassMethod $classMethod): bool
    {
        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($classMethod, MethodCall::class);
        foreach ($methodCalls as $methodCall) {
            $methodCallVar = $methodCall->var;
            if (!$methodCallVar instanceof PropertyFetch) {
                return true;
            }
        }
        return false;
    }
    private function hasInstanceOfExpression(ClassMethod $classMethod): bool
    {
        return (bool) $this->nodeFinder->findFirst($classMethod, function (Node $node): bool {
            return $node instanceof Instanceof_;
        });
    }
}
