<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests;

use Symplify\EasyHydrator\ValueObject\ArrayToValueObjectHydrator;
use Symplify\EasyHydrator\ValueObject\MissingConstructorException;
use Symplify\EasyHydrator\Tests\Fixture\NoConstructor;
use Symplify\EasyHydrator\Tests\HttpKernel\ValueObject\EasyHydratorTestKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class MissingConstructorTest extends AbstractKernelTestCase
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
        $this->expectException(MissingConstructorException::class);

        $this->arrayToValueObjectHydrator->hydrateArray([
            'key' => 'whatever',
        ], NoConstructor::class);
    }
}
