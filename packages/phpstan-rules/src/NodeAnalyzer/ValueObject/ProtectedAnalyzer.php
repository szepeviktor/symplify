<?php

declare(strict_types=1);
namespace Symplify\PHPStanRules\NodeAnalyzer\ValueObject;

use PhpParser\Node\Stmt\Property;
final class ProtectedAnalyzer
{
    /**
     * @var \Symplify\PHPStanRules\NodeAnalyzer\ValueObject\DependencyNodeAnalyzer
     */
    private $dependencyNodeAnalyzer;
    /**
     * @var \Symplify\PHPStanRules\NodeAnalyzer\ValueObject\TypeNodeAnalyzer
     */
    private $typeNodeAnalyzer;
    public function __construct(DependencyNodeAnalyzer $dependencyNodeAnalyzer, TypeNodeAnalyzer $typeNodeAnalyzer)
    {
        $this->dependencyNodeAnalyzer = $dependencyNodeAnalyzer;
        $this->typeNodeAnalyzer = $typeNodeAnalyzer;
    }
    public function isProtectedPropertyOrClassConstAllowed(Property $property): bool
    {
        if ($this->dependencyNodeAnalyzer->isInsideAbstractClassAndPassedAsDependencyViaConstructorOrSetUp($property)) {
            return true;
        }
        if ($this->dependencyNodeAnalyzer->isInsideClassAndPassedAsDependencyViaAutowireMethod($property)) {
            return true;
        }
        return $this->typeNodeAnalyzer->isStaticAndContainerOrKernelType($property);
    }
}
