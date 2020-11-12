<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Release\ReleaseWorker\ValueObject;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\ValueObject\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Utils\ValueObject\VersionUtils;
final class UpdateBranchAliasReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\DevMasterAliasUpdater
     */
    private $devMasterAliasUpdater;
    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider
     */
    private $composerJsonProvider;
    /**
     * @var \Symplify\MonorepoBuilder\Utils\ValueObject\VersionUtils
     */
    private $versionUtils;
    public function __construct(DevMasterAliasUpdater $devMasterAliasUpdater, ComposerJsonProvider $composerJsonProvider, VersionUtils $versionUtils)
    {
        $this->devMasterAliasUpdater = $devMasterAliasUpdater;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->versionUtils = $versionUtils;
    }
    public function work(Version $version): void
    {
        $nextAlias = $this->versionUtils->getNextAliasFormat($version);
        $this->devMasterAliasUpdater->updateFileInfosWithAlias($this->composerJsonProvider->getPackagesComposerFileInfos(), $nextAlias);
    }
    public function getDescription(Version $version): string
    {
        $nextAlias = $this->versionUtils->getNextAliasFormat($version);
        return sprintf('Set branch alias "%s" to all packages', $nextAlias);
    }
}
