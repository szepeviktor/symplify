<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ValueObject\Regex;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ExcessivePublicCountRule\ExcessivePublicCountRuleTest
 */
final class ExcessivePublicCountRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Too many public elements on class - %d. Try narrow it down under %d';
    /**
     * @var int
     */
    private $maxPublicClassElementCount;
    public function __construct(int $maxPublicClassElementCount)
    {
        $this->maxPublicClassElementCount = $maxPublicClassElementCount;
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
        $classPublicElementCount = $this->resolveClassPublicElementCount($node);
        if ($classPublicElementCount < $this->maxPublicClassElementCount) {
            return [];
        }
        $errorMessage = sprintf(self::ERROR_MESSAGE, $classPublicElementCount, $this->maxPublicClassElementCount);
        return [$errorMessage];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new ConfiguredCodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    public $one;

    public $two;

    public $three;
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    public $one;

    public $two;
}
CODE_SAMPLE
, ['maxPublicClassElementCount' => 2])]);
    }
    private function resolveClassPublicElementCount(\PhpParser\Node\Stmt\Class_ $class): int
    {
        $publicElementCount = 0;
        $className = (string) $class->namespacedName;
        foreach ($class->stmts as $classStmt) {
            if ($this->shouldSkipClassStmt($classStmt, $className)) {
                continue;
            }
            ++$publicElementCount;
        }
        return $publicElementCount;
    }
    private function shouldSkipClassStmt(Stmt $classStmt, string $className): bool
    {
        if (!$classStmt instanceof Property && !$classStmt instanceof ClassMethod && !$classStmt instanceof ClassConst) {
            return true;
        }
        if (!$classStmt->isPublic()) {
            return true;
        }
        if (Strings::match($className, Regex::VALUE_OBJECT_REGEX) && $classStmt instanceof ClassConst) {
            return true;
        }
        if ($classStmt instanceof ClassMethod) {
            $methodName = (string) $classStmt->name;
            return Strings::startsWith($methodName, '__');
        }
        return false;
    }
}
