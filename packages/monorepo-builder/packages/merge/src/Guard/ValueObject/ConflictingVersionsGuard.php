<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Merge\Guard\ValueObject;

use Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider;
use Symplify\MonorepoBuilder\ValueObject\ConflictingPackageVersionsReporter;
use Symplify\MonorepoBuilder\ValueObject\VersionValidator;
use Symplify\SymplifyKernel\ValueObject\ShouldNotHappenException;
final class ConflictingVersionsGuard
{
    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider
     */
    private $composerJsonProvider;
    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\ConflictingPackageVersionsReporter
     */
    private $conflictingPackageVersionsReporter;
    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\VersionValidator
     */
    private $versionValidator;
    public function __construct(VersionValidator $versionValidator, ComposerJsonProvider $composerJsonProvider, ConflictingPackageVersionsReporter $conflictingPackageVersionsReporter)
    {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->conflictingPackageVersionsReporter = $conflictingPackageVersionsReporter;
        $this->versionValidator = $versionValidator;
    }
    public function ensureNoConflictingPackageVersions(): void
    {
        $conflictingPackageVersions = $this->versionValidator->findConflictingPackageVersionsInFileInfos($this->composerJsonProvider->getPackagesComposerFileInfos());
        if (count($conflictingPackageVersions) === 0) {
            return;
        }
        $this->conflictingPackageVersionsReporter->report($conflictingPackageVersions);
        throw new ShouldNotHappenException('Fix conflicting package version first');
    }
}
