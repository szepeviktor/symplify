<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use Symplify\EasyCodingStandard\ChangedFilesDetector\ValueObject\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffResultFactory;
use Symplify\EasyCodingStandard\HttpKernel\ValueObject\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class SniffFileProcessorTest extends AbstractKernelTestCase
{
    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @var ErrorAndDiffResultFactory
     */
    private $errorAndDiffResultFactory;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/SniffRunnerSource/easy-coding-standard.php']
        );

        $this->errorAndDiffCollector = self::$container->get(ErrorAndDiffCollector::class);
        $this->errorAndDiffResultFactory = self::$container->get(ErrorAndDiffResultFactory::class);
        $this->sniffFileProcessor = self::$container->get(SniffFileProcessor::class);

        $changedFilesDetector = self::$container->get(ChangedFilesDetector::class);
        $changedFilesDetector->clearCache();
    }

    public function test(): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc');
        $this->sniffFileProcessor->processFile($smartFileInfo);

        $errorAndDiffResult = $this->errorAndDiffResultFactory->create($this->errorAndDiffCollector);

        $this->assertSame(3, $errorAndDiffResult->getErrorCount());
        $this->assertSame(0, $errorAndDiffResult->getFileDiffsCount());
    }
}
