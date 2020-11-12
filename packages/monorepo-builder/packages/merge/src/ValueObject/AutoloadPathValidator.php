<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Merge\ValueObject;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\SmartFileSystem\ValueObject\FileSystemGuard;
final class AutoloadPathValidator
{
    /**
     * @var \Symplify\SmartFileSystem\ValueObject\FileSystemGuard
     */
    private $fileSystemGuard;
    public function __construct(FileSystemGuard $fileSystemGuard)
    {
        $this->fileSystemGuard = $fileSystemGuard;
    }
    public function ensureAutoloadPathExists(ComposerJson $composerJson): void
    {
        $composerJsonFileInfo = $composerJson->getFileInfo();
        if ($composerJsonFileInfo === null) {
            return;
        }
        $autoloadDirectories = $composerJson->getAbsoluteAutoloadDirectories();
        foreach ($autoloadDirectories as $autoloadDirectory) {
            $message = sprintf('In "%s"', $composerJsonFileInfo->getRelativeFilePathFromCwd());
            $this->fileSystemGuard->ensureDirectoryExists($autoloadDirectory, $message);
        }
    }
}
