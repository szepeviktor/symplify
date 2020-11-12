<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ValueObject\ShellCode;
use Symplify\SymfonyStaticDumper\Application\ValueObject\SymfonyStaticDumperApplication;

final class DumpStaticSiteCommand extends AbstractSymplifyCommand
{
    /**
     * @var string
     */
    private $publicDirectory;

    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * @var \Symplify\SymfonyStaticDumper\Application\ValueObject\SymfonyStaticDumperApplication
     */
    private $symfonyStaticDumperApplication;

    public function __construct(
        SymfonyStaticDumperApplication $symfonyStaticDumperApplication,
        ParameterBagInterface $parameterBag
    ) {
        parent::__construct();

        $this->publicDirectory = $parameterBag->get('kernel.project_dir') . '/public';
        $this->outputDirectory = getcwd() . '/output';

        $this->symfonyStaticDumperApplication = $symfonyStaticDumperApplication;
    }

    protected function configure(): void
    {
        $this->setDescription('Dump website to static HTML and CSS in the output directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle->section('Dumping static website');
        $this->symfonyStaticDumperApplication->run($this->publicDirectory, $this->outputDirectory);

        $this->symfonyStyle->note('Run local server to see the output: "php -S localhost:8001 -t output"');

        return ShellCode::SUCCESS;
    }
}
