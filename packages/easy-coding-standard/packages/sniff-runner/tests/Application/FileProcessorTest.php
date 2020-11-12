<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use Symplify\EasyCodingStandard\HttpKernel\ValueObject\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class FileProcessorTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private $initialFileContent;

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    protected function setUp(): void
    {
        static::bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/FileProcessorSource/easy-coding-standard.php']
        );

        $this->sniffFileProcessor = self::$container->get(SniffFileProcessor::class);
    }

    public function test(): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . '/FileProcessorSource/SomeFile.php.inc');

        $this->initialFileContent = $smartFileInfo->getContents();
        $fixedContent = $this->sniffFileProcessor->processFile($smartFileInfo);

        $this->assertNotSame($this->initialFileContent, $fixedContent);
    }

    public function testGetSniffs(): void
    {
        $sniffs = $this->sniffFileProcessor->getCheckers();
        $this->assertCount(1, $sniffs);
    }
}
