<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager;
use Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Merge\Application\MergedAndDecoratedComposerJsonFactory;
use Symplify\MonorepoBuilder\Merge\Guard\ValueObject\ConflictingVersionsGuard;
use Symplify\MonorepoBuilder\ValueObject\SourcesPresenceValidator;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ValueObject\ShellCode;

final class MergeCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var ComposerJsonFactory
     */
    private $composerJsonFactory;

    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\ValueObject\JsonFileManager
     */
    private $jsonFileManager;

    /**
     * @var MergedAndDecoratedComposerJsonFactory
     */
    private $mergedAndDecoratedComposerJsonFactory;

    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\SourcesPresenceValidator
     */
    private $sourcesPresenceValidator;

    /**
     * @var \Symplify\MonorepoBuilder\Merge\Guard\ValueObject\ConflictingVersionsGuard
     */
    private $conflictingVersionsGuard;

    public function __construct(
        ComposerJsonProvider $composerJsonProvider,
        ComposerJsonFactory $composerJsonFactory,
        JsonFileManager $jsonFileManager,
        MergedAndDecoratedComposerJsonFactory $mergedAndDecoratedComposerJsonFactory,
        SourcesPresenceValidator $sourcesPresenceValidator,
        ConflictingVersionsGuard $conflictingVersionsGuard
    ) {
        $this->composerJsonProvider = $composerJsonProvider;
        $this->composerJsonFactory = $composerJsonFactory;
        $this->jsonFileManager = $jsonFileManager;
        $this->mergedAndDecoratedComposerJsonFactory = $mergedAndDecoratedComposerJsonFactory;

        parent::__construct();

        $this->sourcesPresenceValidator = $sourcesPresenceValidator;
        $this->conflictingVersionsGuard = $conflictingVersionsGuard;
    }

    protected function configure(): void
    {
        $this->setDescription('Merge "composer.json" from all found packages to root one');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->sourcesPresenceValidator->validatePackageComposerJsons();

        $this->conflictingVersionsGuard->ensureNoConflictingPackageVersions();

        $mainComposerJsonFilePath = getcwd() . '/composer.json';
        $mainComposerJson = $this->composerJsonFactory->createFromFilePath($mainComposerJsonFilePath);
        $packageFileInfos = $this->composerJsonProvider->getPackagesComposerFileInfos();

        $this->mergedAndDecoratedComposerJsonFactory->createFromRootConfigAndPackageFileInfos(
            $mainComposerJson,
            $packageFileInfos
        );

        $this->jsonFileManager->printComposerJsonToFilePath($mainComposerJson, $mainComposerJsonFilePath);
        $this->symfonyStyle->success('Main "composer.json" was updated.');

        return ShellCode::SUCCESS;
    }
}
