<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use PHP_CodeSniffer\Fixer;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Console\Style\ValueObject\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\Skipper\Skipper\ValueObject\Skipper;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

/**
 * @see \Symplify\EasyCodingStandard\SniffRunner\Tests\File\FileFactoryTest
 */
final class FileFactory
{
    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var \Symplify\Skipper\Skipper\ValueObject\Skipper
     */
    private $skipper;

    /**
     * @var AppliedCheckersCollector
     */
    private $appliedCheckersCollector;

    /**
     * @var \Symplify\EasyCodingStandard\Console\Style\ValueObject\EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    public function __construct(
        Fixer $fixer,
        ErrorAndDiffCollector $errorAndDiffCollector,
        Skipper $skipper,
        AppliedCheckersCollector $appliedCheckersCollector,
        EasyCodingStandardStyle $easyCodingStandardStyle
    ) {
        $this->fixer = $fixer;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->skipper = $skipper;
        $this->appliedCheckersCollector = $appliedCheckersCollector;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    public function createFromFileInfo(SmartFileInfo $smartFileInfo): File
    {
        return new File(
            $smartFileInfo->getRelativeFilePath(),
            $smartFileInfo->getContents(),
            $this->fixer,
            $this->errorAndDiffCollector,
            $this->skipper,
            $this->appliedCheckersCollector,
            $this->easyCodingStandardStyle
        );
    }
}
