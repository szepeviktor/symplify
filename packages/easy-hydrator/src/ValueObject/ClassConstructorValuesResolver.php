<?php

declare(strict_types=1);
namespace Symplify\EasyHydrator\ValueObject;

use ReflectionClass;
use ReflectionMethod;
use Symplify\EasyHydrator\ValueObject\MissingConstructorException;
final class ClassConstructorValuesResolver
{
    /**
     * @var \Symplify\EasyHydrator\ValueObject\TypeCastersCollector
     */
    private $typeCastersCollector;
    /**
     * @var \Symplify\EasyHydrator\ValueObject\ParameterValueResolver
     */
    private $parameterValueResolver;
    public function __construct(TypeCastersCollector $typeCastersCollector, ParameterValueResolver $parameterValueResolver)
    {
        $this->typeCastersCollector = $typeCastersCollector;
        $this->parameterValueResolver = $parameterValueResolver;
    }
    /**
     * @return array<int, mixed>
     */
    public function resolve(string $class, array $data): array
    {
        $arguments = [];
        $constructorMethodReflection = $this->getConstructorMethodReflection($class);
        $parameterReflections = $constructorMethodReflection->getParameters();
        foreach ($parameterReflections as $parameterReflection) {
            $value = $this->parameterValueResolver->getValue($parameterReflection, $data);
            $arguments[] = $this->typeCastersCollector->retype($value, $parameterReflection, $this);
        }
        return $arguments;
    }
    private function getConstructorMethodReflection(string $class): ReflectionMethod
    {
        $reflectionClass = new ReflectionClass($class);
        $constructorReflectionMethod = $reflectionClass->getConstructor();
        if ($constructorReflectionMethod === null) {
            throw new MissingConstructorException(sprintf('Hydrated class "%s" is missing constructor.', $class));
        }
        return $constructorReflectionMethod;
    }
}
