<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\BooleanType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\BoolishClassMethodPrefixRule\BoolishClassMethodPrefixRuleTest
 */
final class BoolishClassMethodPrefixRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method "%s()" returns bool type, so the name should start with is/has/was...';
    /**
     * @var string[]
     */
    private const BOOL_PREFIXES = [
        'is',
        'are',
        'was',
        'will',
        'has',
        'have',
        'had',
        'do',
        'does',
        'di',
        'can',
        'could',
        'should',
        'starts',
        'contains',
        'ends',
        'exists',
        'supports',
        'provide',
        # array access
        'offsetExists',
    ];
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
        return [\PhpParser\Node\Stmt\ClassMethod::class];
    }
    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            throw new ShouldNotHappenException();
        }
        if ($this->shouldSkip($node, $scope, $classReflection)) {
            return [];
        }
        return [sprintf(self::ERROR_MESSAGE, (string) $node->name)];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    public function old(): bool
    {
        return $this->age > 100;
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    public function isOld(): bool
    {
        return $this->age > 100;
    }
}
CODE_SAMPLE
)]);
    }
    private function shouldSkip(\PhpParser\Node\Stmt\ClassMethod $classMethod, Scope $scope, ClassReflection $classReflection): bool
    {
        $methodName = $classMethod->name->toString();
        $returns = $this->findReturnsWithValues($classMethod);
        // nothing was returned
        if ($returns === []) {
            return true;
        }
        $methodReflection = $classReflection->getNativeMethod($methodName);
        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        if (!$returnType instanceof BooleanType && !$this->areOnlyBoolReturnNodes($returns, $scope)) {
            return true;
        }
        if ($this->isMethodNameMatchingBoolPrefixes($methodName)) {
            return true;
        }
        // is required by an interface
        return $this->isMethodRequiredByParentInterface($classReflection, $methodName);
    }
    /**
     * @return Return_[]
     */
    private function findReturnsWithValues(\PhpParser\Node\Stmt\ClassMethod $classMethod): array
    {
        /** @var Return_[] $returns */
        $returns = $this->nodeFinder->findInstanceOf((array) $classMethod->getStmts(), Return_::class);
        $returnsWithValues = [];
        foreach ($returns as $return) {
            if ($return->expr === null) {
                continue;
            }
            $returnsWithValues[] = $return;
        }
        return $returnsWithValues;
    }
    /**
     * @param Return_[] $returns
     */
    private function areOnlyBoolReturnNodes(array $returns, Scope $scope): bool
    {
        foreach ($returns as $return) {
            if ($return->expr === null) {
                continue;
            }
            $returnedNodeType = $scope->getType($return->expr);
            if (!$returnedNodeType instanceof BooleanType) {
                return false;
            }
        }
        return true;
    }
    private function isMethodNameMatchingBoolPrefixes(string $methodName): bool
    {
        $prefixesPattern = '#^(' . implode('|', self::BOOL_PREFIXES) . ')#';
        return (bool) Strings::match($methodName, $prefixesPattern);
    }
    private function isMethodRequiredByParentInterface(ClassReflection $classReflection, string $methodName): bool
    {
        $interfaces = $classReflection->getInterfaces();
        foreach ($interfaces as $interface) {
            if ($interface->hasMethod($methodName)) {
                return true;
            }
        }
        return false;
    }
}
