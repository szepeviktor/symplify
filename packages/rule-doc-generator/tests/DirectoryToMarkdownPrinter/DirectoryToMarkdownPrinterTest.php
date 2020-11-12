<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter;

use Iterator;
use Symplify\EasyTesting\DataProvider\ValueObject\StaticFixtureUpdater;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\RuleDocGenerator\ValueObject\DirectoryToMarkdownPrinter;
use Symplify\RuleDocGenerator\HttpKernel\ValueObject\RuleDocGeneratorKernel;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class DirectoryToMarkdownPrinterTest extends AbstractKernelTestCase
{
    /**
     * @var \Symplify\RuleDocGenerator\ValueObject\DirectoryToMarkdownPrinter
     */
    private $directoryToMarkdownPrinter;

    protected function setUp(): void
    {
        $this->bootKernel(RuleDocGeneratorKernel::class);
        $this->directoryToMarkdownPrinter = self::$container->get(DirectoryToMarkdownPrinter::class);
    }

    /**
     * @dataProvider provideDataPHPStan()
     * @dataProvider provideDataPHPCSFixer()
     */
    public function test(SmartFileInfo $directoryFileInfo, string $expectedFile): void
    {
        $fileContent = $this->directoryToMarkdownPrinter->printDirectory($directoryFileInfo);

        $expectedFileInfo = new SmartFileInfo($expectedFile);
        StaticFixtureUpdater::updateExpectedFixtureContent($fileContent, $expectedFileInfo);

        $this->assertStringEqualsFile($expectedFile, $fileContent);
    }

    public function provideDataPHPStan(): Iterator
    {
        yield [new SmartFileInfo(__DIR__ . '/Fixture/PHPStan'), __DIR__ . '/Expected/phpstan/phpstan_content.md'];
        yield [
            new SmartFileInfo(__DIR__ . '/Fixture/ConfigurablePHPStan'),
            __DIR__ . '/Expected/phpstan/configurable_phpstan_content.md',
        ];
    }

    public function provideDataPHPCSFixer(): Iterator
    {
        yield [
            new SmartFileInfo(__DIR__ . '/Fixture/PHPCSFixer'),
            __DIR__ . '/Expected/php-cs-fixer/phpcsfixer_content.md',
        ];
        yield [
            new SmartFileInfo(__DIR__ . '/Fixture/ConfigurablePHPCSFixer'),
            __DIR__ . '/Expected/php-cs-fixer/configurable_phpcsfixer_content.md',
        ];
    }
}
