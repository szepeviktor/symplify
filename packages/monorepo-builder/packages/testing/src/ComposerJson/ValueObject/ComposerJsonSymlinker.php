<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Testing\ComposerJson\ValueObject;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Testing\PathResolver\ValueObject\PackagePathResolver;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
/**
 * @see \Symplify\MonorepoBuilder\Testing\Tests\ComposerJson\ComposerJsonSymlinkerTest
 */
final class ComposerJsonSymlinker
{
    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider
     */
    private $composerJsonProvider;
    /**
     * @var \Symplify\MonorepoBuilder\Testing\PathResolver\ValueObject\PackagePathResolver
     */
    private $packagePathResolver;
    public function __construct(ComposerJsonProvider $composerJsonProvider, PackagePathResolver $packagePathResolver)
    {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->packagePathResolver = $packagePathResolver;
    }
    /**
     * @param mixed[] $packageComposerJson
     * @param string[] $packageNames
     * @return mixed[]
     */
    public function decoratePackageComposerJsonWithPackageSymlinks(array $packageComposerJson, array $packageNames, SmartFileInfo $mainComposerJsonFileInfo): array
    {
        // @see https://getcomposer.org/doc/05-repositories.md#path
        foreach ($packageNames as $packageName) {
            $usedPackageFileInfo = $this->composerJsonProvider->getPackageFileInfoByName($packageName);
            $relativePathToLocalPackage = $this->packagePathResolver->resolveRelativePathToLocalPackage($mainComposerJsonFileInfo, $usedPackageFileInfo);
            $repositoriesContent = [
                'type' => 'path',
                'url' => $relativePathToLocalPackage,
                // we need hard copy of files, as in normal composer install of standalone package
                'options' => ['symlink' => false],
            ];
            if (array_key_exists(ComposerJsonSection::REPOSITORIES, $packageComposerJson)) {
                array_unshift($packageComposerJson[ComposerJsonSection::REPOSITORIES], $repositoriesContent);
            } else {
                $packageComposerJson[ComposerJsonSection::REPOSITORIES][] = $repositoriesContent;
            }
        }
        return $packageComposerJson;
    }
}
