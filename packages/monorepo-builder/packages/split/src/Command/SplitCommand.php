<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\Split\Configuration\ValueObject\RepositoryGuard;
use Symplify\MonorepoBuilder\Split\FileSystem\ValueObject\DirectoryToRepositoryProvider;
use Symplify\MonorepoBuilder\Split\ValueObject\PackageToRepositorySplitter;
use Symplify\MonorepoBuilder\ValueObject\File;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ValueObject\ShellCode;
use Symplify\PackageBuilder\Parameter\ValueObject\ParameterProvider;

final class SplitCommand extends AbstractSymplifyCommand
{
    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var \Symplify\MonorepoBuilder\Split\Configuration\ValueObject\RepositoryGuard
     */
    private $repositoryGuard;

    /**
     * @var \Symplify\MonorepoBuilder\Split\ValueObject\PackageToRepositorySplitter
     */
    private $packageToRepositorySplitter;

    /**
     * @var \Symplify\MonorepoBuilder\Split\FileSystem\ValueObject\DirectoryToRepositoryProvider
     */
    private $directoryToRepositoryProvider;

    public function __construct(
        RepositoryGuard $repositoryGuard,
        ParameterProvider $parameterProvider,
        PackageToRepositorySplitter $packageToRepositorySplitter,
        DirectoryToRepositoryProvider $directoryToRepositoryProvider
    ) {
        parent::__construct();

        $this->repositoryGuard = $repositoryGuard;
        $this->packageToRepositorySplitter = $packageToRepositorySplitter;
        $this->directoryToRepositoryProvider = $directoryToRepositoryProvider;
        $this->rootDirectory = $parameterProvider->provideStringParameter(Option::ROOT_DIRECTORY);
    }

    protected function configure(): void
    {
        $description = sprintf(
            'Splits monorepo packages to standalone repositories as defined in "%s" section of "%s" config.',
            '$parameters->set(Option::DIRECTORIES_REPOSITORY, [...])',
            File::CONFIG
        );

        $this->setDescription($description);

        $this->addOption(
            Option::BRANCH,
            null,
            InputOption::VALUE_OPTIONAL,
            'Branch to run split on, defaults to current branch'
        );

        $this->addOption(
            Option::MAX_PROCESSES,
            null,
            InputOption::VALUE_REQUIRED,
            'Maximum number of processes to run in parallel'
        );

        $this->addOption(
            Option::TAG,
            't',
            InputOption::VALUE_REQUIRED,
            'Specify the Git tag use for split. Use the most recent one by default'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle->warning(
            'This command is deprecated and will be removed in the future. No worries, switch to GitHub Action, that can do the work much faster and more reliable, then this poor wrapper around bash that wraps a git hacking.'
        );
        // to get the attention
        sleep(3);

        $this->repositoryGuard->ensureIsRepositoryDirectory($this->rootDirectory);

        $maxProcesses = $input->getOption(Option::MAX_PROCESSES) ? (int)
        $input->getOption(Option::MAX_PROCESSES)
            : null;

        /** @var string|null $tag */
        $tag = $input->getOption(Option::TAG);

        $branch = $input->getOption(Option::BRANCH) ? (string) $input->getOption(Option::BRANCH) : null;

        $resolvedDirectoriesToRepository = $this->directoryToRepositoryProvider->provide();
        if (count($resolvedDirectoriesToRepository) === 0) {
            $this->symfonyStyle->error('No packages to split');
            return ShellCode::SUCCESS;
        }

        $this->symfonyStyle->title('Splitting Following Packages');

        foreach ($resolvedDirectoriesToRepository as $directory => $gitRepository) {
            $message = sprintf('* "%s" directory to "%s" repository', $directory, $gitRepository);
            $this->symfonyStyle->writeln($message);
        }

        $this->symfonyStyle->newLine(2);

        // to give time to process split information
        sleep(2);

        $this->packageToRepositorySplitter->splitDirectoriesToRepositories(
            $resolvedDirectoriesToRepository,
            $this->rootDirectory,
            $branch,
            $maxProcesses,
            $tag
        );

        $message = sprintf('Split of %d packages was successful', count($resolvedDirectoriesToRepository));
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
