<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Throw_;
use PHPStan\Analyser\Scope;
use ReflectionClass;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Throwable;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoDefaultExceptionRule\NoDefaultExceptionRuleTest
 */
final class NoDefaultExceptionRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use custom exceptions instead of native "%s"';
    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [\PhpParser\Node\Stmt\Throw_::class];
    }
    /**
     * @param Throw_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (!$node->expr instanceof \PhpParser\Node\Expr\New_) {
            return [];
        }
        /** @var New_ $new */
        $new = $node->expr;
        if (!$new->class instanceof Name) {
            return [];
        }
        $exceptionClass = (string) $new->class;
        if (!is_a($exceptionClass, Throwable::class, true)) {
            return [];
        }
        $reflectionClass = new ReflectionClass($exceptionClass);
        if (!$reflectionClass->isInternal()) {
            return [];
        }
        return [sprintf(self::ERROR_MESSAGE, $exceptionClass)];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
throw new RuntimeException('...');
CODE_SAMPLE
, <<<'CODE_SAMPLE'
use App\Exception\FileNotFoundExceptoin;

throw new FileNotFoundException('...');
CODE_SAMPLE
)]);
    }
}
