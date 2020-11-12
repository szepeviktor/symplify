<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\ValueObject\DependencyUpdater;
use Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider;
use Symplify\MonorepoBuilder\ValueObject\SourcesPresenceValidator;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ValueObject\ShellCode;

final class BumpInterdependencyCommand extends AbstractSymplifyCommand
{
    /**
     * @var string
     */
    private const VERSION_ARGUMENT = 'version';

    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\DependencyUpdater
     */
    private $dependencyUpdater;

    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ValueObject\ComposerJsonProvider
     */
    private $composerJsonProvider;

    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\SourcesPresenceValidator
     */
    private $sourcesPresenceValidator;

    public function __construct(
        DependencyUpdater $dependencyUpdater,
        ComposerJsonProvider $composerJsonProvider,
        SourcesPresenceValidator $sourcesPresenceValidator
    ) {
        parent::__construct();

        $this->dependencyUpdater = $dependencyUpdater;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->sourcesPresenceValidator = $sourcesPresenceValidator;
    }

    protected function configure(): void
    {
        $this->setDescription('Bump dependency of split packages on each other');
        $this->addArgument(
            self::VERSION_ARGUMENT,
            InputArgument::REQUIRED,
            'New version of inter-dependencies, e.g. "^4.4.2"'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->sourcesPresenceValidator->validateRootComposerJsonName();

        /** @var string $version */
        $version = $input->getArgument(self::VERSION_ARGUMENT);

        $mainComposerJson = $this->composerJsonProvider->getRootJson();

        // @todo resolve better for only found packages
        // see https://github.com/symplify/symplify/pull/1037/files
        [$vendor] = explode('/', $mainComposerJson['name']);

        $this->dependencyUpdater->updateFileInfosWithVendorAndVersion(
            $this->composerJsonProvider->getPackagesComposerFileInfos(),
            $vendor,
            $version
        );

        $successMessage = sprintf('Inter-dependencies of packages were updated to "%s".', $version);
        $this->symfonyStyle->success($successMessage);

        return ShellCode::SUCCESS;
    }
}
