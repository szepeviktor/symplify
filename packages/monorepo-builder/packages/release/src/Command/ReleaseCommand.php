<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\Release\Configuration\ValueObject\StageResolver;
use Symplify\MonorepoBuilder\Release\Configuration\ValueObject\VersionResolver;
use Symplify\MonorepoBuilder\Release\Output\ValueObject\ReleaseWorkerReporter;
use Symplify\MonorepoBuilder\Release\ValueObject\ReleaseWorkerProvider;
use Symplify\MonorepoBuilder\Release\ValueObject\SemVersion;
use Symplify\MonorepoBuilder\Release\ValueObject\Stage;
use Symplify\MonorepoBuilder\ValueObject\SourcesPresenceValidator;
use Symplify\MonorepoBuilder\ValueObject\File;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ValueObject\ShellCode;

final class ReleaseCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\MonorepoBuilder\Release\ValueObject\ReleaseWorkerProvider
     */
    private $releaseWorkerProvider;

    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\SourcesPresenceValidator
     */
    private $sourcesPresenceValidator;

    /**
     * @var \Symplify\MonorepoBuilder\Release\Configuration\ValueObject\StageResolver
     */
    private $stageResolver;

    /**
     * @var \Symplify\MonorepoBuilder\Release\Configuration\ValueObject\VersionResolver
     */
    private $versionResolver;

    /**
     * @var \Symplify\MonorepoBuilder\Release\Output\ValueObject\ReleaseWorkerReporter
     */
    private $releaseWorkerReporter;

    public function __construct(
        ReleaseWorkerProvider $releaseWorkerProvider,
        SourcesPresenceValidator $sourcesPresenceValidator,
        StageResolver $stageResolver,
        VersionResolver $versionResolver,
        ReleaseWorkerReporter $releaseWorkerReporter
    ) {
        parent::__construct();

        $this->releaseWorkerProvider = $releaseWorkerProvider;
        $this->sourcesPresenceValidator = $sourcesPresenceValidator;
        $this->stageResolver = $stageResolver;
        $this->versionResolver = $versionResolver;
        $this->releaseWorkerReporter = $releaseWorkerReporter;
    }

    protected function configure(): void
    {
        $this->setDescription('Perform release process with set Release Workers.');

        $description = sprintf(
            'Release version, in format "<major>.<minor>.<patch>" or "v<major>.<minor>.<patch> or one of keywords: "%s"',
            implode('", "', SemVersion::ALL)
        );
        $this->addArgument(Option::VERSION, InputArgument::REQUIRED, $description);

        $this->addOption(
            Option::DRY_RUN,
            null,
            InputOption::VALUE_NONE,
            'Do not perform operations, just their preview'
        );

        $this->addOption(Option::STAGE, null, InputOption::VALUE_REQUIRED, 'Name of stage to perform', Stage::MAIN);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->sourcesPresenceValidator->validateRootComposerJsonName();

        // validation phase
        $stage = $this->stageResolver->resolveFromInput($input);

        $activeReleaseWorkers = $this->releaseWorkerProvider->provideByStage($stage);
        if ($activeReleaseWorkers === []) {
            $errorMessage = sprintf(
                'There are no release workers registered. Be sure to add them to "%s"',
                File::CONFIG
            );
            $this->symfonyStyle->error($errorMessage);

            return ShellCode::ERROR;
        }

        $totalWorkerCount = count($activeReleaseWorkers);
        $i = 0;
        $isDryRun = (bool) $input->getOption(Option::DRY_RUN);
        $version = $this->versionResolver->resolveVersion($input, $stage);

        foreach ($activeReleaseWorkers as $releaseWorker) {
            $title = sprintf('%d/%d) %s', ++$i, $totalWorkerCount, $releaseWorker->getDescription($version));
            $this->symfonyStyle->title($title);
            $this->releaseWorkerReporter->printMetadata($releaseWorker);

            if (! $isDryRun) {
                $releaseWorker->work($version);
            }
        }

        if ($isDryRun) {
            $this->symfonyStyle->note('Running in dry mode, nothing is changed');
        } elseif ($stage === Stage::MAIN) {
            $message = sprintf('Version "%s" is now released!', $version->getVersionString());
            $this->symfonyStyle->success($message);
        } else {
            $finishedMessage = sprintf(
                'Stage "%s" for version "%s" is now finished!',
                $stage,
                $version->getVersionString()
            );
            $this->symfonyStyle->success($finishedMessage);
        }

        return ShellCode::SUCCESS;
    }
}
