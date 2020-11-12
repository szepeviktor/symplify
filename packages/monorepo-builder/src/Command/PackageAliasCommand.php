<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\ValueObject\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\Finder\ValueObject\PackageComposerFinder;
use Symplify\MonorepoBuilder\Git\ValueObject\ExpectedAliasResolver;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ValueObject\ShellCode;

final class PackageAliasCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\MonorepoBuilder\Finder\ValueObject\PackageComposerFinder
     */
    private $packageComposerFinder;

    /**
     * @var \Symplify\MonorepoBuilder\ValueObject\DevMasterAliasUpdater
     */
    private $devMasterAliasUpdater;

    /**
     * @var \Symplify\MonorepoBuilder\Git\ValueObject\ExpectedAliasResolver
     */
    private $expectedAliasResolver;

    public function __construct(
        PackageComposerFinder $packageComposerFinder,
        DevMasterAliasUpdater $devMasterAliasUpdater,
        ExpectedAliasResolver $expectedAliasResolver
    ) {
        parent::__construct();

        $this->packageComposerFinder = $packageComposerFinder;
        $this->devMasterAliasUpdater = $devMasterAliasUpdater;
        $this->expectedAliasResolver = $expectedAliasResolver;
    }

    protected function configure(): void
    {
        $this->setDescription('Updates branch alias in "composer.json" all found packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerPackageFiles = $this->packageComposerFinder->getPackageComposerFiles();
        if (count($composerPackageFiles) === 0) {
            $this->symfonyStyle->error('No "composer.json" were found in packages.');
            return ShellCode::ERROR;
        }

        $expectedAlias = $this->expectedAliasResolver->resolve();

        $this->devMasterAliasUpdater->updateFileInfosWithAlias($composerPackageFiles, $expectedAlias);

        $message = sprintf('Alias "dev-master" was updated to "%s" in all packages.', $expectedAlias);
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
