<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoEntityManagerInControllerRule\NoEntityManagerInControllerRuleTest
 */
final class NoEntityManagerInControllerRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use specific repository over entity manager in Controller';
    /**
     * @var string
     * @see https://regex101.com/r/hJt00N/1
     */
    private const CONTROLLER_PRESENTER_REGEX = '#(Controller|Presenter)$#';
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
        if ((string) $node->name !== MethodName::CONSTRUCTOR) {
            return [];
        }
        if (!$this->isInControllerClass($scope)) {
            return [];
        }
        foreach ($node->params as $param) {
            if (!$this->isEntityManagerParam($param)) {
                continue;
            }
            return [self::ERROR_MESSAGE];
        }
        return [];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
final class SomeController
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        // ...
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
final class SomeController
{
    public function __construct(AnotherRepository $anotherRepository)
    {
        // ...
    }
}
CODE_SAMPLE
)]);
    }
    private function isInControllerClass(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return false;
        }
        return (bool) Strings::match($classReflection->getName(), self::CONTROLLER_PRESENTER_REGEX);
    }
    private function isEntityManagerParam(Param $param): bool
    {
        if ($param->type === null) {
            return false;
        }
        if (!$param->type instanceof Name) {
            return false;
        }
        $paramType = $param->type->toString();
        if ($paramType === 'Doctrine\ORM\EntityManager') {
            return true;
        }
        return is_a($paramType, 'Doctrine\ORM\EntityManagerInterface', true);
    }
}
