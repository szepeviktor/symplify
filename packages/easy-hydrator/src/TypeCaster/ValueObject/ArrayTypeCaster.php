<?php

declare(strict_types=1);
namespace Symplify\EasyHydrator\TypeCaster\ValueObject;

use ReflectionParameter;
use Symplify\EasyHydrator\ValueObject\ClassConstructorValuesResolver;
use Symplify\EasyHydrator\Contract\TypeCasterInterface;
use Symplify\EasyHydrator\ValueObject\ParameterTypeRecognizer;
final class ArrayTypeCaster implements TypeCasterInterface
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
        return $type === 'array';
    }
    /**
     * @return mixed[]|null
     */
    public function retype($value, ReflectionParameter $reflectionParameter, ClassConstructorValuesResolver $classConstructorValuesResolver): ?array
    {
        $type = $this->parameterTypeRecognizer->getTypeFromDocBlock($reflectionParameter);
        if ($value === null && $reflectionParameter->allowsNull()) {
            return null;
        }
        return array_map(static function ($value) use ($type) {
            if ($type === 'string') {
                return (string) $value;
            }
            if ($type === 'bool') {
                return (bool) $value;
            }
            if ($type === 'int') {
                return (int) $value;
            }
            if ($type === 'float') {
                return (float) $value;
            }
            return $value;
        }, $value);
    }
    public function getPriority(): int
    {
        return 8;
    }
}
