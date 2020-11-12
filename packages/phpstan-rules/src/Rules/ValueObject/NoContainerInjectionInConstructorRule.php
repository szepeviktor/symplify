<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PHPStanRules\Types\ValueObject\ContainsTypeAnalyser;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoContainerInjectionInConstructorRule\NoContainerInjectionInConstructorRuleTest
 */
final class NoContainerInjectionInConstructorRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of container injection, use specific service';
    /**
     * @var \Symplify\PHPStanRules\Types\ValueObject\ContainsTypeAnalyser
     */
    private $containsTypeAnalyser;
    public function __construct(ContainsTypeAnalyser $containsTypeAnalyser)
    {
        $this->containsTypeAnalyser = $containsTypeAnalyser;
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
        if (!$this->isInConstructMethod($scope)) {
            return [];
        }
        if (!$this->containsTypeAnalyser->containsExprTypes($node, $scope, [ContainerInterface::class])) {
            return [];
        }
        if ($this->containsTypeAnalyser->containsExprTypes($node, $scope, [ContainerBuilder::class])) {
            return [];
        }
        return [self::ERROR_MESSAGE];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    public function __construct(ContainerInterface $container)
    {
        $this->someDependency = $container->get('...');
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    public function __construct(SomeDependency $someDependency)
    {
        $this->someDependency = $someDependency;
    }
}
CODE_SAMPLE
)]);
    }
    private function isInConstructMethod(Scope $scope): bool
    {
        $reflectionFunction = $scope->getFunction();
        if (!$reflectionFunction instanceof MethodReflection) {
            return false;
        }
        return $reflectionFunction->getName() === MethodName::CONSTRUCTOR;
    }
}
