<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use ReflectionClass;
use Symplify\PHPStanRules\Naming\ValueObject\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayDestructRule\ForbiddenArrayDestructRuleTest
 */
final class ForbiddenArrayDestructRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Array destruct is not allowed. Use value object to pass data instead';
    /**
     * @var string
     * @see https://regex101.com/r/dhGhYp/1
     */
    public const VENDOR_DIRECTORY_REGEX = '#/vendor/#';
    /**
     * @var \Symplify\PHPStanRules\Naming\ValueObject\SimpleNameResolver
     */
    private $simpleNameResolver;
    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
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
        if (!$node->var instanceof Array_) {
            return [];
        }
        // swaps are allowed
        if ($node->expr instanceof Array_) {
            return [];
        }
        if ($this->isAllowedCall($node)) {
            return [];
        }
        // is 3rd party method call → nothing we can do about it
        if ($this->isVendorProvider($node, $scope)) {
            return [];
        }
        return [self::ERROR_MESSAGE];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
final class SomeClass
{
    public function run(): void
    {
        [$firstValue, $secondValue] = $this->getRandomData();
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run(): void
    {
        $valueObject = $this->getValueObject();
        $firstValue = $valueObject->getFirstValue();
        $secondValue = $valueObject->getSecondValue();
    }
}
CODE_SAMPLE
)]);
    }
    private function isAllowedCall(\PhpParser\Node\Expr\Assign $assign): bool
    {
        // "explode()" is allowed
        if ($assign->expr instanceof FuncCall && $this->simpleNameResolver->isName($assign->expr->name, 'explode')) {
            return true;
        }
        // Strings::split() is allowed
        return $assign->expr instanceof StaticCall && $this->simpleNameResolver->isName($assign->expr->name, 'split');
    }
    private function isVendorProvider(\PhpParser\Node\Expr\Assign $assign, Scope $scope): bool
    {
        if (!$assign->expr instanceof MethodCall) {
            return false;
        }
        $callerType = $scope->getType($assign->expr->var);
        if (!$callerType instanceof ObjectType) {
            return false;
        }
        $reflectionClass = new ReflectionClass($callerType->getClassName());
        return (bool) Strings::match((string) $reflectionClass->getFileName(), self::VENDOR_DIRECTORY_REGEX);
    }
}
