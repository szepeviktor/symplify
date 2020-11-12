<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Nop;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Naming\ValueObject\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\ParentClassMethodNodeResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Throwable;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule\NoParentMethodCallOnEmptyStatementInParentMethodRuleTest
 */
final class NoParentMethodCallOnEmptyStatementInParentMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not call parent method if parent method is empty';
    /**
     * @var \Symplify\PHPStanRules\Naming\ValueObject\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Symplify\PHPStanRules\ValueObject\ParentClassMethodNodeResolver
     */
    private $parentClassMethodNodeResolver;
    public function __construct(SimpleNameResolver $simpleNameResolver, ParentClassMethodNodeResolver $parentClassMethodNodeResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->parentClassMethodNodeResolver = $parentClassMethodNodeResolver;
    }
    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [\PhpParser\Node\Expr\StaticCall::class];
    }
    /**
     * @param StaticCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->class instanceof Expr) {
            return [];
        }
        $className = $node->class->toString();
        if ($className !== 'parent') {
            return [];
        }
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }
        // skip exceptions
        if (is_a($classReflection->getName(), Throwable::class, true)) {
            return [];
        }
        $methodName = $this->simpleNameResolver->getName($node->name);
        if ($methodName === null) {
            return [];
        }
        $parentClassMethodStmtCount = $this->resolveParentClassMethodStmtCount($scope, $methodName);
        if ($parentClassMethodStmtCount === 0) {
            return [self::ERROR_MESSAGE];
        }
        return [];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
class ParentClass
{
    public function someMethod()
    {
    }
}

class SomeClass extends ParentClass
{
    public function someMethod()
    {
        parent::someMethod();
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class ParentClass
{
    public function someMethod()
    {
    }
}

class SomeClass extends ParentClass
{
    public function someMethod()
    {
    }
}
CODE_SAMPLE
)]);
    }
    private function resolveParentClassMethodStmtCount(Scope $scope, string $methodName): int
    {
        $parentClassMethodNodes = $this->parentClassMethodNodeResolver->resolveParentClassMethodNodes($scope, $methodName);
        $countStmts = 0;
        foreach ($parentClassMethodNodes as $stmt) {
            // ensure empty statement not counted
            if ($stmt instanceof Nop) {
                continue;
            }
            ++$countStmts;
        }
        return $countStmts;
    }
}
