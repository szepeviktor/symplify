<?php

declare(strict_types=1);
namespace Symplify\EasyCodingStandard\FileSystem\ValueObject;

use Symplify\EasyCodingStandard\ChangedFilesDetector\ValueObject\ChangedFilesDetector;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
final class FileFilter
{
    /**
     * @var \Symplify\EasyCodingStandard\ChangedFilesDetector\ValueObject\ChangedFilesDetector
     */
    private $changedFilesDetector;
    public function __construct(ChangedFilesDetector $changedFilesDetector)
    {
        $this->changedFilesDetector = $changedFilesDetector;
    }
    /**
     * @param \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[] $fileInfos
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    public function filterOnlyChangedFiles(array $fileInfos): array
    {
        return array_filter($fileInfos, function (SmartFileInfo $smartFileInfo): bool {
            return $this->changedFilesDetector->hasFileInfoChanged($smartFileInfo);
        });
    }
}
