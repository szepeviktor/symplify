<?php

declare(strict_types=1);
namespace Symplify\EasyCodingStandard\Guard\ValueObject;

use Symplify\EasyCodingStandard\Application\ValueObject\FileProcessorCollector;
use Symplify\EasyCodingStandard\Configuration\ValueObject\NoCheckersLoadedException;
final class LoadedCheckersGuard
{
    /**
     * @var \Symplify\EasyCodingStandard\Application\ValueObject\FileProcessorCollector
     */
    private $fileProcessorCollector;
    public function __construct(FileProcessorCollector $fileProcessorCollector)
    {
        $this->fileProcessorCollector = $fileProcessorCollector;
    }
    public function ensureSomeCheckersAreRegistered(): void
    {
        $checkerCount = $this->getCheckerCount();
        if ($checkerCount !== 0) {
            return;
        }
        throw new NoCheckersLoadedException();
    }
    private function getCheckerCount(): int
    {
        $checkerCount = 0;
        $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
        foreach ($fileProcessors as $fileProcessor) {
            $checkerCount += count($fileProcessor->getCheckers());
        }
        return $checkerCount;
    }
}
