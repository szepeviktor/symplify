<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\PackageBuilder\Matcher\ValueObject\ArrayStringAndFnMatcher;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenParentClassRule\ForbiddenParentClassRuleTest
 */
final class ForbiddenParentClassRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" inherits from forbidden parent class "%s". Use "%s" instead';
    /**
     * @var string
     */
    public const COMPOSITION_OVER_INHERITANCE = 'composition over inheritance';
    /**
     * @var \Symplify\PackageBuilder\Matcher\ValueObject\ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;
    /**
     * @var array<string, string|null>
     * Null, if there is no preference. Just forbidden
     */
    private $forbiddenParentClassesWithPreferences = [];
    /**
     * @param string[] $forbiddenParentClasses
     * @param string[] $forbiddenParentClassesWithPreferences
     */
    public function __construct(ArrayStringAndFnMatcher $arrayStringAndFnMatcher, array $forbiddenParentClasses = [], array $forbiddenParentClassesWithPreferences = [])
    {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
        $this->forbiddenParentClassesWithPreferences = $forbiddenParentClassesWithPreferences;
        foreach ($forbiddenParentClasses as $forbiddenParentClass) {
            $this->forbiddenParentClassesWithPreferences[$forbiddenParentClass] = null;
        }
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
        $shortClassName = $node->name;
        if ($shortClassName === null) {
            return [];
        }
        // no parent
        if ($node->extends === null) {
            return [];
        }
        $currentParentClass = $node->extends->toString();
        foreach ($this->forbiddenParentClassesWithPreferences as $forbiddenParentClass => $preference) {
            if (!$this->arrayStringAndFnMatcher->isMatch($currentParentClass, [$forbiddenParentClass])) {
                continue;
            }
            // allow inheritance
            if ($preference !== null && $node->isAbstract()) {
                continue;
            }
            $class = $node->namespacedName->toString();
            $errorMessage = $this->createErrorMessage($preference, $class, $currentParentClass);
            return [$errorMessage];
        }
        return [];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new ConfiguredCodeSample(<<<'CODE_SAMPLE'
class SomeClass extends ParentClass
{
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    public function __construct(DecoupledClass $decoupledClass)
    {
        $this->decoupledClass = $decoupledClass;
    }
}
CODE_SAMPLE
, ['forbiddenParentClasses' => ['ParentClass']])]);
    }
    private function createErrorMessage(?string $preference, string $class, string $currentParentClass): string
    {
        $preferenceMessage = $preference ?? self::COMPOSITION_OVER_INHERITANCE;
        return sprintf(self::ERROR_MESSAGE, $class, $currentParentClass, $preferenceMessage);
    }
}
