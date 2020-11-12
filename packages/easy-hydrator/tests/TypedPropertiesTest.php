<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests;

use Symplify\EasyHydrator\ValueObject\ArrayToValueObjectHydrator;
use Symplify\EasyHydrator\Tests\Fixture\TypedProperty;
use Symplify\EasyHydrator\Tests\HttpKernel\ValueObject\EasyHydratorTestKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

/**
 * @requires PHP >= 7.4
 */
final class TypedPropertiesTest extends AbstractKernelTestCase
{
    /**
     * @var \Symplify\EasyHydrator\ValueObject\ArrayToValueObjectHydrator
     */
    private $arrayToValueObjectHydrator;

    protected function setUp(): void
    {
        $this->bootKernel(EasyHydratorTestKernel::class);

        $this->arrayToValueObjectHydrator = self::$container->get(ArrayToValueObjectHydrator::class);
    }

    public function test(): void
    {
        $typedProperty = $this->arrayToValueObjectHydrator->hydrateArray(['value' => 'yay'], TypedProperty::class);

        $this->assertInstanceOf(TypedProperty::class, $typedProperty);

        /** @var TypedProperty $typedProperty */
        $this->assertSame('yay', $typedProperty->getValue());
    }
}
