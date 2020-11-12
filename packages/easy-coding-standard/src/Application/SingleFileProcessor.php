<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Application\ValueObject\FileProcessorCollector;
use ParseError;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ValueObject\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\Skipper\Skipper\ValueObject\Skipper;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class SingleFileProcessor
{
    /**
     * @var \Symplify\Skipper\Skipper\ValueObject\Skipper
     */
    private $skipper;

    /**
     * @var \Symplify\EasyCodingStandard\ChangedFilesDetector\ValueObject\ChangedFilesDetector
     */
    private $changedFilesDetector;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var \Symplify\EasyCodingStandard\Application\ValueObject\FileProcessorCollector
     */
    private $fileProcessorCollector;

    public function __construct(
        Skipper $skipper,
        ChangedFilesDetector $changedFilesDetector,
        ErrorAndDiffCollector $errorAndDiffCollector,
        FileProcessorCollector $fileProcessorCollector
    ) {
        $this->skipper = $skipper;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->fileProcessorCollector = $fileProcessorCollector;
    }

    public function processFileInfo(SmartFileInfo $smartFileInfo): void
    {
        try {
            $this->changedFilesDetector->addFileInfo($smartFileInfo);
            $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
            foreach ($fileProcessors as $fileProcessor) {
                if ($fileProcessor->getCheckers() === []) {
                    continue;
                }

                if ($this->skipper->shouldSkipFileInfo($smartFileInfo)) {
                    continue;
                }

                $fileProcessor->processFile($smartFileInfo);
            }
        } catch (ParseError $parseError) {
            $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
            $this->errorAndDiffCollector->addSystemErrorMessage(
                $smartFileInfo,
                $parseError->getLine(),
                $parseError->getMessage()
            );
        }
    }
}
