<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Symplify\ConsoleColorDiff\Console\Formatter\ValueObject\ColorConsoleDiffFormatter;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class FileDiffFactory
{
    /**
     * @var \Symplify\ConsoleColorDiff\Console\Formatter\ValueObject\ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;

    public function __construct(ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }

    /**
     * @param string[] $appliedCheckers
     */
    public function createFromDiffAndAppliedCheckers(
        SmartFileInfo $smartFileInfo,
        string $diff,
        array $appliedCheckers
    ): FileDiff {
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);

        return new FileDiff($smartFileInfo, $diff, $consoleFormattedDiff, $appliedCheckers);
    }
}
