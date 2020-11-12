<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Release\ReleaseWorker\ValueObject;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ValueObject\ProcessRunner;
use Symplify\MonorepoBuilder\Utils\ValueObject\VersionUtils;
final class PushNextDevReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var \Symplify\MonorepoBuilder\Release\Process\ValueObject\ProcessRunner
     */
    private $processRunner;
    /**
     * @var \Symplify\MonorepoBuilder\Utils\ValueObject\VersionUtils
     */
    private $versionUtils;
    public function __construct(ProcessRunner $processRunner, VersionUtils $versionUtils)
    {
        $this->processRunner = $processRunner;
        $this->versionUtils = $versionUtils;
    }
    public function work(Version $version): void
    {
        $versionInString = $this->getVersionDev($version);
        $gitAddCommitCommand = sprintf('git add . && git commit --allow-empty -m "open %s" && git push origin master', $versionInString);
        $this->processRunner->run($gitAddCommitCommand);
    }
    public function getDescription(Version $version): string
    {
        $versionInString = $this->getVersionDev($version);
        return sprintf('Push "%s" open to remote repository', $versionInString);
    }
    private function getVersionDev(Version $version): string
    {
        return $this->versionUtils->getNextAliasFormat($version);
    }
}
