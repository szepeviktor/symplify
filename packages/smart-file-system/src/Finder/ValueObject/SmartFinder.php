<?php

declare(strict_types=1);
namespace Symplify\SmartFileSystem\Finder\ValueObject;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\ValueObject\FileSystemFilter;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
/**
 * @see \Symplify\SmartFileSystem\Tests\Finder\SmartFinder\SmartFinderTest
 */
final class SmartFinder
{
    /**
     * @var \Symplify\SmartFileSystem\Finder\ValueObject\FinderSanitizer
     */
    private $finderSanitizer;
    /**
     * @var \Symplify\SmartFileSystem\ValueObject\FileSystemFilter
     */
    private $fileSystemFilter;
    public function __construct(FinderSanitizer $finderSanitizer, FileSystemFilter $fileSystemFilter)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->fileSystemFilter = $fileSystemFilter;
    }
    /**
     * @param string[] $excludedDirectories
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    public function find(array $directoriesOrFiles, string $name, array $excludedDirectories = []): array
    {
        $directories = $this->fileSystemFilter->filterDirectories($directoriesOrFiles);
        $fileInfos = [];
        if (count($directories) > 0) {
            $finder = new Finder();
            $finder->name($name)->in($directories)->files()->sortByName();
            if ($excludedDirectories !== []) {
                $finder->exclude($excludedDirectories);
            }
            $fileInfos = $this->finderSanitizer->sanitize($finder);
        }
        $files = $this->fileSystemFilter->filterFiles($directoriesOrFiles);
        foreach ($files as $file) {
            $fileInfos[] = new SmartFileInfo($file);
        }
        return $fileInfos;
    }
}
