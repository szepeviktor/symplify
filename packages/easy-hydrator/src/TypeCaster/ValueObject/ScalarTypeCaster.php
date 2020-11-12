<?php

declare(strict_types=1);
namespace Symplify\EasyHydrator\TypeCaster\ValueObject;

use ReflectionParameter;
use Symplify\EasyHydrator\ValueObject\ClassConstructorValuesResolver;
use Symplify\EasyHydrator\Contract\TypeCasterInterface;
use Symplify\EasyHydrator\ValueObject\ParameterTypeRecognizer;
final class ScalarTypeCaster implements TypeCasterInterface
{
    /**
     * @var \Symplify\EasyHydrator\ValueObject\ParameterTypeRecognizer
     */
    private $parameterTypeRecognizer;
    public function __construct(ParameterTypeRecognizer $parameterTypeRecognizer)
    {
        $this->parameterTypeRecognizer = $parameterTypeRecognizer;
    }
    public function isSupported(ReflectionParameter $reflectionParameter): bool
    {
        $type = $this->parameterTypeRecognizer->getType($reflectionParameter);
        return in_array($type, ['string', 'bool', 'int'], true);
    }
    public function retype($value, ReflectionParameter $reflectionParameter, ClassConstructorValuesResolver $classConstructorValuesResolver)
    {
        $type = $this->parameterTypeRecognizer->getType($reflectionParameter);
        if ($value === null && $reflectionParameter->allowsNull()) {
            return null;
        }
        if ($type === 'string') {
            return (string) $value;
        }
        if ($type === 'bool') {
            return (bool) $value;
        }
        if ($type === 'int') {
            return (int) $value;
        }
        return $value;
    }
    public function getPriority(): int
    {
        return 10;
    }
}
