<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\ChangedFilesDetector\ValueObject\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Configuration\ValueObject\Configuration;
use Symplify\EasyCodingStandard\Console\Style\ValueObject\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FileSystem\ValueObject\FileFilter;
use Symplify\EasyCodingStandard\Finder\ValueObject\SourceFinder;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class EasyCodingStandardApplication
{
    /**
     * @var \Symplify\EasyCodingStandard\Console\Style\ValueObject\EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var \Symplify\EasyCodingStandard\Finder\ValueObject\SourceFinder
     */
    private $sourceFinder;

    /**
     * @var \Symplify\EasyCodingStandard\ChangedFilesDetector\ValueObject\ChangedFilesDetector
     */
    private $changedFilesDetector;

    /**
     * @var \Symplify\EasyCodingStandard\Configuration\ValueObject\Configuration
     */
    private $configuration;

    /**
     * @var \Symplify\EasyCodingStandard\FileSystem\ValueObject\FileFilter
     */
    private $fileFilter;

    /**
     * @var SingleFileProcessor
     */
    private $singleFileProcessor;

    public function __construct(
        EasyCodingStandardStyle $easyCodingStandardStyle,
        SourceFinder $sourceFinder,
        ChangedFilesDetector $changedFilesDetector,
        Configuration $configuration,
        FileFilter $fileFilter,
        SingleFileProcessor $singleFileProcessor
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->sourceFinder = $sourceFinder;
        $this->changedFilesDetector = $changedFilesDetector;
        $this->configuration = $configuration;
        $this->fileFilter = $fileFilter;
        $this->singleFileProcessor = $singleFileProcessor;
    }

    public function run(): int
    {
        // 1. find files in sources
        $files = $this->sourceFinder->find(
            $this->configuration->getSources(),
            $this->configuration->doesMatchGitDiff()
        );

        // 2. clear cache
        if ($this->configuration->shouldClearCache()) {
            $this->changedFilesDetector->clearCache();
        } else {
            $files = $this->fileFilter->filterOnlyChangedFiles($files);
        }

        // no files found
        $filesCount = count($files);
        if ($filesCount === 0) {
            return 0;
        }

        // 3. start progress bar
        if ($this->configuration->shouldShowProgressBar() && ! $this->easyCodingStandardStyle->isDebug()) {
            $this->easyCodingStandardStyle->progressStart($filesCount);

            // show more data on progres bar
            if ($this->easyCodingStandardStyle->isVerbose()) {
                $this->easyCodingStandardStyle->enableDebugProgressBar();
            }
        }

        // 4. process found files by each processors
        $this->processFoundFiles($files);

        return $filesCount;
    }

    /**
     * @param \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[] $fileInfos
     */
    private function processFoundFiles(array $fileInfos): void
    {
        foreach ($fileInfos as $fileInfo) {
            if ($this->easyCodingStandardStyle->isDebug()) {
                $this->easyCodingStandardStyle->writeln(' [file] ' . $fileInfo->getRelativeFilePathFromCwd());
            }

            $this->singleFileProcessor->processFileInfo($fileInfo);

            if (! $this->easyCodingStandardStyle->isDebug() && $this->configuration->shouldShowProgressBar()) {
                $this->easyCodingStandardStyle->progressAdvance();
            }
        }
    }
}
