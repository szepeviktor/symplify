<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider;
use Symplify\MonorepoBuilder\ValueObject\ConflictingPackageVersionsReporter;
use Symplify\MonorepoBuilder\ValueObject\SourcesPresenceValidator;
use Symplify\MonorepoBuilder\ValueObject\VersionValidator;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ValueObject\ShellCode;

final class ValidateCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\VersionValidator
     */
    private $versionValidator;

    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\ConflictingPackageVersionsReporter
     */
    private $conflictingPackageVersionsReporter;

    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\SourcesPresenceValidator
     */
    private $sourcesPresenceValidator;

    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        VersionValidator $versionValidator,
        ConflictingPackageVersionsReporter $conflictingPackageVersionsReporter,
        SourcesPresenceValidator $sourcesPresenceValidator
    ) {
        parent::__construct();

        $this->versionValidator = $versionValidator;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->conflictingPackageVersionsReporter = $conflictingPackageVersionsReporter;
        $this->sourcesPresenceValidator = $sourcesPresenceValidator;
    }

    protected function configure(): void
    {
        $this->setDescription('Validates synchronized versions in "composer.json" in all found packages.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->sourcesPresenceValidator->validatePackageComposerJsons();

        $conflictingPackageVersions = $this->versionValidator->findConflictingPackageVersionsInFileInfos(
            $this->composerJsonProvider->getRootAndPackageFileInfos()
        );
        if ($conflictingPackageVersions === []) {
            $this->symfonyStyle->success('All packages "composer.json" files use same package versions.');

            return ShellCode::SUCCESS;
        }

        $this->conflictingPackageVersionsReporter->report($conflictingPackageVersions);

        return ShellCode::ERROR;
    }
}
