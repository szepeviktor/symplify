<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\File;

use PHP_CodeSniffer\Files\File as PhpCodeSnifferFile;
use PHP_CodeSniffer\Fixer;
use Symplify\EasyCodingStandard\HttpKernel\ValueObject\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class FileFactoryTest extends AbstractKernelTestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $this->bootKernel(\Symplify\EasyCodingStandard\HttpKernel\ValueObject\EasyCodingStandardKernel::class);
        $this->fileFactory = self::$container->get(FileFactory::class);
    }

    public function test(): void
    {
        $fileInfo = new \Symplify\SmartFileSystem\ValueObject\SmartFileInfo(__DIR__ . '/FileFactorySource/SomeFile.php');
        $file = $this->fileFactory->createFromFileInfo($fileInfo);
        $this->assertInstanceOf(File::class, $file);
        $this->assertInstanceOf(PhpCodeSnifferFile::class, $file);
        $this->assertInstanceOf(Fixer::class, $file->fixer);
    }
}
