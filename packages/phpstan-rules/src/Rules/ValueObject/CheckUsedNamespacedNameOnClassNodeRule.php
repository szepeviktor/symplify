<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckUsedNamespacedNameOnClassNodeRule\CheckUsedNamespacedNameOnClassNodeRuleTest
 */
final class CheckUsedNamespacedNameOnClassNodeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use "$class->namespaceName" instead of "$class->name" that only returns short class name';
    /**
     * @var string[]
     */
    private $excludedClasses = [];
    /**
     * @param string[] $excludedClasses
     */
    public function __construct(array $excludedClasses = [])
    {
        $this->excludedClasses = $excludedClasses;
    }
    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [\PhpParser\Node\Expr\Variable::class];
    }
    /**
     * @param Variable $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $type = $scope->getType($node);
        if (!method_exists($type, 'getClassName')) {
            return [];
        }
        if ($type->getClassName() !== Class_::class) {
            return [];
        }
        $next = $node->getAttribute(PHPStanAttributeKey::NEXT);
        if ($next === null) {
            return [];
        }
        if ($next->name !== 'name') {
            return [];
        }
        if ($this->isVariableNamedShortClassName($node)) {
            return [];
        }
        /** @var Class_|null $class */
        $class = $this->getFirstParentByType($node, Class_::class);
        if ($class === null) {
            return [];
        }
        if (in_array($class->namespacedName->toString(), $this->excludedClasses, true)) {
            return [];
        }
        return [self::ERROR_MESSAGE];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
use PhpParser\Node\Stmt\Class_;

final class SomeClass
{
    public function run(Class_ $class): bool
    {
        $className = (string) $class->name;
        return class_exists($className);
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
use PhpParser\Node\Stmt\Class_;

final class SomeClass
{
    public function run(Class_ $class): bool
    {
        $className = (string) $class->namespacedName;
        return class_exists($className);
    }
}
CODE_SAMPLE
)]);
    }
    private function isVariableNamedShortClassName(\PhpParser\Node\Expr\Variable $variable): bool
    {
        /** @var Assign|null $assign */
        $assign = $this->getFirstParentByType($variable, Assign::class);
        if (!$assign instanceof Assign) {
            return false;
        }
        /** @var Variable $classNameVariable */
        $classNameVariable = $assign->var;
        /** @var Identifier $classNameIdentifier */
        $classNameIdentifier = $classNameVariable->name;
        $classNameVariableName = (string) $classNameIdentifier;
        if ($classNameVariableName !== 'shortClassName') {
            return false;
        }
        return true;
    }
}
