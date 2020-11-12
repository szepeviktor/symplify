<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\Rules\ValueObject;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenPrivateMethodByTypeRule\ForbiddenPrivateMethodByTypeRuleTest
 */
final class ForbiddenPrivateMethodByTypeRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Private method in is not allowed here - it should only delegate to others. Decouple the private method to a new service class';
    /**
     * @var array<string, string>
     */
    private $forbiddenTypes = [];
    /**
     * @param array<string, string> $forbiddenTypes
     */
    public function __construct(array $forbiddenTypes = [])
    {
        $this->forbiddenTypes = $forbiddenTypes;
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
        if (!$node->isPrivate()) {
            return [];
        }
        $className = $this->resolveCurrentClassName($node);
        if ($className === null) {
            return [];
        }
        if ($this->isInAbstractClass($node)) {
            return [];
        }
        foreach ($this->forbiddenTypes as $forbiddenType) {
            if (!is_a($className, $forbiddenType, true)) {
                continue;
            }
            return [self::ERROR_MESSAGE];
        }
        return [];
    }
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new ConfiguredCodeSample(<<<'CODE_SAMPLE'
class SomeCommand extends Command
{
    public function run()
    {
        $this->somePrivateMethod();
    }

    private function somePrivateMethod()
    {
        // ...
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeCommand extends Command
{
    /**
     * @var ExternalService
     */
    private $externalService;

    public function __construct(ExternalService $externalService)
    {
        $this->externalService = $externalService;
    }

    public function run()
    {
        $this->externalService->someMethod();
    }
}
CODE_SAMPLE
, ['forbiddenTypes' => ['Command']])]);
    }
}
