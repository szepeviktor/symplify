<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests;

use Symplify\EasyHydrator\ValueObject\ArrayToValueObjectHydrator;
use Symplify\EasyHydrator\ValueObject\MissingDataException;
use Symplify\EasyHydrator\Tests\Fixture\DefaultValuesConstructor;
use Symplify\EasyHydrator\Tests\HttpKernel\ValueObject\EasyHydratorTestKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class DefaultValuesHydratorTest extends AbstractKernelTestCase
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

    public function testExceptionWillBeThrownWhenMissingDataForNonOptionalParameter(): void
    {
        $this->expectException(MissingDataException::class);

        $this->arrayToValueObjectHydrator->hydrateArray([], DefaultValuesConstructor::class);
    }

    public function testDefaultValues(): void
    {
        $data = [
            'foo' => null,
            'bar' => 'baz',
        ];

        /** @var DefaultValuesConstructor $object */
        $object = $this->arrayToValueObjectHydrator->hydrateArray($data, DefaultValuesConstructor::class);

        self::assertNull($object->getFoo());
        self::assertNull($object->getPerson());
        self::assertSame('baz', $object->getBar());
    }
}
