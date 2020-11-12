<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use Symplify\PackageBuilder\Console\ValueObject\ShellCode;
use Symplify\RuleDocGenerator\ValueObject\DirectoryToMarkdownPrinter;
use Symplify\RuleDocGenerator\ValueObject\Option;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class GenerateCommand extends AbstractSymplifyCommand
{
    /**
     * @var \Symplify\RuleDocGenerator\ValueObject\DirectoryToMarkdownPrinter
     */
    private $directoryToMarkdownPrinter;

    public function __construct(DirectoryToMarkdownPrinter $directoryToMarkdownPrinter)
    {
        parent::__construct();

        $this->directoryToMarkdownPrinter = $directoryToMarkdownPrinter;
    }

    protected function configure(): void
    {
        $this->setDescription('Generated Markdown documentation based on documented rules found in directory');
        $this->addArgument(Option::PATH, InputArgument::REQUIRED, 'Path to directory of your project');
        $this->addOption(
            Option::OUTPUT,
            null,
            InputOption::VALUE_REQUIRED,
            'Path to output generated markdown file',
            getcwd() . '/docs/rules_overview.md'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string) $input->getArgument(Option::PATH);

        $directoryFileInfo = new SmartFileInfo($path);
        $markdownFileContent = $this->directoryToMarkdownPrinter->printDirectory($directoryFileInfo);

        // dump markdown file
        $outputFilePath = (string) $input->getOption(Option::OUTPUT);
        $this->smartFileSystem->dumpFile($outputFilePath, $markdownFileContent);

        $outputFileInfo = new SmartFileInfo($outputFilePath);
        $message = sprintf('File "%s" was created', $outputFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->success($message);

        return ShellCode::SUCCESS;
    }
}
