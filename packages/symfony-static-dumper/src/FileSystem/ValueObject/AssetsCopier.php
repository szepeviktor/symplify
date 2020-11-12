<?php

declare(strict_types=1);
namespace Symplify\SymfonyStaticDumper\FileSystem\ValueObject;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\ValueObject\FinderSanitizer;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;
/**
 * @see \Symplify\SymfonyStaticDumper\Tests\FileSystem\AssetsCopierTest
 */
final class AssetsCopier
{
    /**
     * @var \Symplify\SmartFileSystem\Finder\ValueObject\FinderSanitizer
     */
    private $finderSanitizer;
    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(FinderSanitizer $finderSanitizer, SmartFileSystem $smartFileSystem)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->smartFileSystem = $smartFileSystem;
    }
    public function copyAssets(string $publicDirectory, string $outputDirectory): void
    {
        $assetFileInfos = $this->findAssetFileInfos($publicDirectory);
        foreach ($assetFileInfos as $assetFileInfo) {
            $relativePathFromRoot = $assetFileInfo->getRelativeFilePathFromDirectory($publicDirectory);
            $this->smartFileSystem->copy($assetFileInfo->getRealPath(), $outputDirectory . '/' . $relativePathFromRoot);
        }
    }
    /**
     * @return \Symplify\SmartFileSystem\ValueObject\SmartFileInfo[]
     */
    private function findAssetFileInfos(string $publicDirectory): array
    {
        $finder = new Finder();
        $finder->files()->in($publicDirectory)->notName('*.php');
        return $this->finderSanitizer->sanitize($finder);
    }
}
