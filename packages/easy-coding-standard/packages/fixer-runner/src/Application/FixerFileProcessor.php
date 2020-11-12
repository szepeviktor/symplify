<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\EasyCodingStandard\Application\AbstractFileProcessor;
use Symplify\EasyCodingStandard\Configuration\ValueObject\Configuration;
use Symplify\EasyCodingStandard\Console\Style\ValueObject\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\FixerRunner\ValueObject\Application\FixerFailedException;
use Symplify\EasyCodingStandard\FixerRunner\Parser\ValueObject\FileToTokensParser;
use Symplify\Skipper\Skipper\ValueObject\Skipper;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;
use Throwable;

/**
 * @see \Symplify\EasyCodingStandard\Tests\Error\ErrorCollector\FixerFileProcessorTest
 */
final class FixerFileProcessor extends AbstractFileProcessor
{
    /**
     * @var class-string[]
     */
    private $appliedFixers = [];

    /**
     * @var FixerInterface[]
     */
    private $fixers = [];

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var \Symplify\Skipper\Skipper\ValueObject\Skipper
     */
    private $skipper;

    /**
     * @var \Symplify\EasyCodingStandard\Configuration\ValueObject\Configuration
     */
    private $configuration;

    /**
     * @var \Symplify\EasyCodingStandard\FixerRunner\Parser\ValueObject\FileToTokensParser
     */
    private $fileToTokensParser;

    /**
     * @var DifferInterface
     */
    private $differ;

    /**
     * @var \Symplify\EasyCodingStandard\Console\Style\ValueObject\EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @param FixerInterface[] $fixers
     */
    public function __construct(
        ErrorAndDiffCollector $errorAndDiffCollector,
        Configuration $configuration,
        FileToTokensParser $fileToTokensParser,
        Skipper $skipper,
        DifferInterface $differ,
        EasyCodingStandardStyle $easyCodingStandardStyle,
        SmartFileSystem $smartFileSystem,
        array $fixers = []
    ) {
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->skipper = $skipper;
        $this->configuration = $configuration;
        $this->fileToTokensParser = $fileToTokensParser;
        $this->differ = $differ;
        $this->fixers = $this->sortFixers($fixers);
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->smartFileSystem = $smartFileSystem;
    }

    /**
     * @return FixerInterface[]
     */
    public function getCheckers(): array
    {
        return $this->fixers;
    }

    public function processFile(SmartFileInfo $smartFileInfo): string
    {
        $tokens = $this->fileToTokensParser->parseFromFilePath($smartFileInfo->getRealPath());

        $this->appliedFixers = [];
        foreach ($this->fixers as $fixer) {
            if ($this->shouldSkipForMarkdownHeredocCheck($fixer)) {
                continue;
            }

            $this->processTokensByFixer($smartFileInfo, $tokens, $fixer);
        }

        $contents = $smartFileInfo->getContents();
        if ($this->appliedFixers === []) {
            return $contents;
        }

        $diff = $this->differ->diff($contents, $tokens->generateCode());
        // some fixer with feature overlap can null each other
        if ($diff === '') {
            return $contents;
        }

        // file has changed
        $targetFileInfo = $this->resolveTargetFileInfo($smartFileInfo);
        $this->errorAndDiffCollector->addDiffForFileInfo($targetFileInfo, $diff, $this->appliedFixers);

        $tokenGeneratedCode = $tokens->generateCode();
        if ($this->configuration->isFixer()) {
            $this->smartFileSystem->dumpFile($smartFileInfo->getRealPath(), $tokenGeneratedCode);
        }

        Tokens::clearCache();

        return $tokenGeneratedCode;
    }

    /**
     * @param FixerInterface[] $fixers
     * @return FixerInterface[]
     */
    private function sortFixers(array $fixers): array
    {
        usort($fixers, function (FixerInterface $firstFixer, FixerInterface $secondFixer): int {
            return $secondFixer->getPriority() <=> $firstFixer->getPriority();
        });

        return $fixers;
    }

    private function processTokensByFixer(SmartFileInfo $smartFileInfo, Tokens $tokens, FixerInterface $fixer): void
    {
        if ($this->shouldSkip($smartFileInfo, $fixer, $tokens)) {
            return;
        }

        // show current fixer in --debug / -vvv
        if ($this->easyCodingStandardStyle->isDebug()) {
            $this->easyCodingStandardStyle->writeln('     [fixer] ' . get_class($fixer));
        }

        try {
            $fixer->fix($smartFileInfo, $tokens);
        } catch (Throwable $throwable) {
            throw new FixerFailedException(sprintf(
                'Fixing of "%s" file by "%s" failed: %s in file %s on line %d',
                $smartFileInfo->getRelativeFilePath(),
                get_class($fixer),
                $throwable->getMessage(),
                $throwable->getFile(),
                $throwable->getLine()
            ), $throwable->getCode(), $throwable);
        }

        if (! $tokens->isChanged()) {
            return;
        }

        $tokens->clearEmptyTokens();
        $tokens->clearChanged();

        $this->appliedFixers[] = get_class($fixer);
    }

    private function shouldSkip(SmartFileInfo $smartFileInfo, FixerInterface $fixer, Tokens $tokens): bool
    {
        if ($this->skipper->shouldSkipElementAndFileInfo($fixer, $smartFileInfo)) {
            return true;
        }

        if (! $fixer->supports($smartFileInfo)) {
            return true;
        }

        return ! $fixer->isCandidate($tokens);
    }

    /**
     * Is markdown/herenow doc checker → skip useless rules
     */
    private function shouldSkipForMarkdownHeredocCheck(FixerInterface $fixer): bool
    {
        if ($this->currentParentFileInfoProvider->provide() === null) {
            return false;
        }

        if ($fixer instanceof BlankLineAfterOpeningTagFixer) {
            return true;
        }

        if ($fixer instanceof DeclareStrictTypesFixer) {
            return true;
        }

        if ($fixer instanceof SingleBlankLineBeforeNamespaceFixer) {
            return true;
        }

        return $fixer instanceof SingleBlankLineAtEofFixer;
    }
}
