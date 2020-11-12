<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Application;

use Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication;
use Symplify\EasyCodingStandard\HttpKernel\ValueObject\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class ApplicationTest extends AbstractKernelTestCase
{
    /**
     * @var EasyCodingStandardApplication
     */
    private $easyCodingStandardApplication;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->easyCodingStandardApplication = self::$container->get(EasyCodingStandardApplication::class);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testRun(): void
    {
        $this->easyCodingStandardApplication->run();
    }
}
