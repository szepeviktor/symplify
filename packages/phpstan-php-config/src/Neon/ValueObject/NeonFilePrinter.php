<?php

declare(strict_types=1);
namespace Symplify\PHPStanPHPConfig\Neon\ValueObject;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;
final class NeonFilePrinter
{
    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(SmartFileSystem $smartFileSystem, \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle)
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->symfonyStyle = $symfonyStyle;
    }
    public function printContentToOutputFile(string $neonFileContent, string $outputFilePath): void
    {
        $this->smartFileSystem->dumpFile($outputFilePath, $neonFileContent);
        $outputFileInfo = new SmartFileInfo($outputFilePath);
        $message = sprintf('The neon file was converted to "%s"', $outputFileInfo->getRelativeFilePathFromCwd());
        $this->symfonyStyle->success($message);
        $this->symfonyStyle->writeln('===================================');
        $this->symfonyStyle->newLine(1);
        $this->symfonyStyle->writeln('<comment>' . $neonFileContent . '</comment>');
        $this->symfonyStyle->writeln('===================================');
        $this->symfonyStyle->newLine(1);
    }
}
