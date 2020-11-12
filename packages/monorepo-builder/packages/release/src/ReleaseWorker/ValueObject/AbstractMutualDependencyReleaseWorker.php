<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Release\ReleaseWorker\ValueObject;

use Symplify\MonorepoBuilder\ValueObject\DependencyUpdater;
use Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Package\ValueObject\PackageNamesProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Utils\ValueObject\VersionUtils;
abstract class AbstractMutualDependencyReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider
     */
    protected $composerJsonProvider;
    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\DependencyUpdater
     */
    protected $dependencyUpdater;
    /**
     * @var \Symplify\MonorepoBuilder\Utils\ValueObject\VersionUtils
     */
    protected $versionUtils;
    /**
     * @var \Symplify\MonorepoBuilder\Package\ValueObject\PackageNamesProvider
     */
    protected $packageNamesProvider;
    public function __construct(ComposerJsonProvider $composerJsonProvider, DependencyUpdater $dependencyUpdater, PackageNamesProvider $packageNamesProvider, VersionUtils $versionUtils)
    {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->dependencyUpdater = $dependencyUpdater;
        $this->versionUtils = $versionUtils;
        $this->packageNamesProvider = $packageNamesProvider;
    }
}
