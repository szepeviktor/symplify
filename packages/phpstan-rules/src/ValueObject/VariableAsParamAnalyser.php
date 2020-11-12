<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\ValueObject;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\Php\PhpMethodFromParserNodeReflection;
use Symplify\PackageBuilder\Reflection\ValueObject\PrivatesAccessor;
use Symplify\PHPStanRules\ValueObject\MethodName;
final class VariableAsParamAnalyser
{
    /**
     * @var \Symplify\PackageBuilder\Reflection\ValueObject\PrivatesAccessor
     */
    private $privatesAccessor;
    public function __construct(PrivatesAccessor $privatesAccessor)
    {
        $this->privatesAccessor = $privatesAccessor;
    }
    public function isVariableFromConstructorParam(MethodReflection $methodReflection, Variable $variable): bool
    {
        if ($methodReflection->getName() !== MethodName::CONSTRUCTOR) {
            return false;
        }
        if (!$methodReflection instanceof PhpMethodFromParserNodeReflection) {
            return false;
        }
        $constructorClassMethod = $this->privatesAccessor->getPrivateProperty($methodReflection, 'functionLike');
        if (!$constructorClassMethod instanceof ClassMethod) {
            return false;
        }
        if ($variable->name instanceof Expr) {
            return false;
        }
        $variableName = (string) $variable->name;
        foreach ($constructorClassMethod->params as $param) {
            $paramName = (string) $param->var->name;
            if ($variableName === $paramName) {
                return true;
            }
        }
        return false;
    }
}
