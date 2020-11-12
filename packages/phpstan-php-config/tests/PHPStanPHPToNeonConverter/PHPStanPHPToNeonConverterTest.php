<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\Tests\PHPStanPHPToNeonConverter;

use Iterator;
use Symplify\EasyTesting\DataProvider\ValueObject\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PHPStanPHPConfig\HttpKernel\ValueObject\PHPStanPHPConfigKernel;
use Symplify\PHPStanPHPConfig\PHPStanPHPToNeonConverter;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;
use Symplify\SmartFileSystem\ValueObject\SmartFileSystem;

final class PHPStanPHPToNeonConverterTest extends AbstractKernelTestCase
{
    /**
     * @var PHPStanPHPToNeonConverter
     */
    private $phpStanPHPToNeonConverter;

    /**
     * @var \Symplify\SmartFileSystem\ValueObject\SmartFileSystem
     */
    private $smartFileSystem;

    protected function setUp(): void
    {
        $this->bootKernel(PHPStanPHPConfigKernel::class);
        $this->phpStanPHPToNeonConverter = self::$container->get(PHPStanPHPToNeonConverter::class);
        $this->smartFileSystem = self::$container->get(SmartFileSystem::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        // for path check
        $temporaryPath = StaticFixtureSplitter::getTemporaryPath();
        $this->smartFileSystem->mkdir($temporaryPath . '/existing_path');
        $this->smartFileSystem->copy(
            __DIR__ . '/Fixture/import/config/rules.neon',
            $temporaryPath . '/config/rules.neon'
        );

        $splitFileInfoToLocalInputAndExpected = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpected(
            $fixtureFileInfo
        );

        $convertedContent = $this->phpStanPHPToNeonConverter->convert(
            $splitFileInfoToLocalInputAndExpected->getInputFileInfo()
        );
        $this->assertSame($splitFileInfoToLocalInputAndExpected->getExpected(), $convertedContent);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.php');
    }
}
