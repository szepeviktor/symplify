<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PackageBuilder\Matcher\ValueObject\ArrayStringAndFnMatcher;
use Symplify\PHPStanRules\Naming\ValueObject\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoClassWithStaticMethodWithoutStaticNameRule\NoClassWithStaticMethodWithoutStaticNameRuleTest
 */
final class NoClassWithStaticMethodWithoutStaticNameRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" with static method must have "Static" in its name it explicit';
    /**
     * @var string[]
     */
    private const ALLOWED_CLASS_TYPES = [
        // symfony classes with static methods
        EventSubscriberInterface::class,
        Command::class,
    ];
    /**
     * @var NodeFinder
     */
    private $nodeFinder;
    /**
     * @var \Symplify\PHPStanRules\Naming\ValueObject\SimpleNameResolver
     */
    private $simpleNameResolver;
    /**
     * @var \Symplify\PackageBuilder\Matcher\ValueObject\ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;
    public function __construct(\PhpParser\NodeFinder $nodeFinder, SimpleNameResolver $simpleNameResolver, ArrayStringAndFnMatcher $arrayStringAndFnMatcher)
    {
        $this->nodeFinder = $nodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
    }
    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [\PhpParser\Node\Stmt\Class_::class];
    }
    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (!$this->isClassWithStaticMethod($node)) {
            return [];
        }
        // skip anonymous class
        $shortClassName = (string) $node->name;
        if ($shortClassName === '') {
            return [];
        }
        // already has "Static" in the name
        if (Strings::contains((string) $shortClassName, 'Static')) {
            return [];
        }
        $currentFullyQualifiedClassName = $this->resolveCurrentClassName($node);
        if ($currentFullyQualifiedClassName === null) {
            return [];
        }
        if ($this->shouldSkipClassName($currentFullyQualifiedClassName)) {
            return [];
        }
        $errorMessage = sprintf(self::ERROR_MESSAGE, $currentFullyQualifiedClassName);
        return [$errorMessage];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    public static function getSome()
    {
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeStaticClass
{
    public static function getSome()
    {
    }
}
CODE_SAMPLE
)]);
    }
    private function isClassWithStaticMethod($node): bool
    {
        $classMethods = $node->getMethods();
        foreach ($classMethods as $classMethod) {
            if (!$classMethod->isStatic()) {
                continue;
            }
            if ($this->isStaticConstructorOfValueObject($classMethod)) {
                continue;
            }
            return true;
        }
        return false;
    }
    private function shouldSkipClassName(string $classShortName): bool
    {
        return $this->arrayStringAndFnMatcher->isMatchWithIsA($classShortName, self::ALLOWED_CLASS_TYPES);
    }
    private function isStaticConstructorOfValueObject(ClassMethod $classMethod): bool
    {
        return (bool) $this->nodeFinder->findFirst((array) $classMethod->stmts, function (Node $node): bool {
            if (!$node instanceof Return_) {
                return false;
            }
            $returnedExpr = $node->expr;
            if (!$returnedExpr instanceof New_) {
                return false;
            }
            return $this->simpleNameResolver->isName($returnedExpr->class, 'self');
        });
    }
}
