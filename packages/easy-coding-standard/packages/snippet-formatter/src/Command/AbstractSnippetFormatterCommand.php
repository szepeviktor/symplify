<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter;
use Symplify\PackageBuilder\Console\ValueObject\ShellCode;
use Symplify\SmartFileSystem\Finder\ValueObject\SmartFinder;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;

abstract class AbstractSnippetFormatterCommand extends AbstractCheckCommand
{
    /**
     * @var SnippetFormatter
     */
    private $snippetFormatter;

    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var \Symplify\SmartFileSystem\Finder\ValueObject\SmartFinder
     */
    private $smartFinder;

    /**
     * @required
     */
    public function autowireAbstractSnippetFormatterCommand(
        SnippetFormatter $snippetFormatter,
        SmartFileSystem $smartFileSystem,
        SmartFinder $smartFinder
    ): void {
        $this->snippetFormatter = $snippetFormatter;
        $this->smartFileSystem = $smartFileSystem;
        $this->smartFinder = $smartFinder;
    }

    protected function doExecuteSnippetFormatterWithFileNamesAndSnippetPattern(
        InputInterface $input,
        string $fileNames,
        string $snippetPattern,
        string $kind
    ): int {
        $this->configuration->resolveFromInput($input);

        $sources = $this->configuration->getSources();
        $phpFileInfos = $this->smartFinder->find($sources, $fileNames, ['Fixture']);

        $fileCount = count($phpFileInfos);
        if ($fileCount === 0) {
            return $this->printNoFilesFoundWarningAndExitSuccess($sources, $fileNames);
        }

        $this->easyCodingStandardStyle->progressStart($fileCount);
        foreach ($phpFileInfos as $phpFileInfo) {
            $this->processFileInfoWithPattern($phpFileInfo, $snippetPattern, $kind);
            $this->easyCodingStandardStyle->progressAdvance();
        }

        return $this->reportProcessedFiles($fileCount);
    }

    private function processFileInfoWithPattern(SmartFileInfo $phpFileInfo, string $snippetPattern, string $kind): void
    {
        $fixedContent = $this->snippetFormatter->format($phpFileInfo, $snippetPattern, $kind);
        if ($phpFileInfo->getContents() === $fixedContent) {
            // nothing has changed
            return;
        }

        if (! $this->configuration->isFixer()) {
            return;
        }

        $this->smartFileSystem->dumpFile($phpFileInfo->getPathname(), $fixedContent);
    }

    /**
     * @param string[] $sources
     */
    private function printNoFilesFoundWarningAndExitSuccess(array $sources, string $type): int
    {
        $warningMessage = sprintf(
            'No "%s" files found in "%s" paths.%sCheck CLI arguments or "Option::PATHS" parameter in "ecs.php" config file',
            $type,
            implode('", ', $sources),
            PHP_EOL
        );

        $this->easyCodingStandardStyle->warning($warningMessage);

        return ShellCode::SUCCESS;
    }
}
