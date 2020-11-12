<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Tests\HeredocNowdoc;

use Iterator;
use Symplify\EasyCodingStandard\Configuration\ValueObject\Configuration;
use Symplify\EasyCodingStandard\HttpKernel\ValueObject\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
use Symplify\EasyTesting\DataProvider\ValueObject\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

/**
 * @requires PHP >= 7.3
 * For testing approach @see https://github.com/symplify/easy-testing
 */
final class Php73Test extends AbstractKernelTestCase
{
    /**
     * @var SnippetFormatter
     */
    private $snippetFormatter;

    protected function setUp(): void
    {
        self::bootKernelWithConfigs(EasyCodingStandardKernel::class, [__DIR__ . '/config/array_fixer.php']);
        $this->snippetFormatter = self::$container->get(SnippetFormatter::class);

        // enable fixing
        /** @var \Symplify\EasyCodingStandard\Configuration\ValueObject\Configuration $configuration */
        $configuration = self::$container->get(Configuration::class);
        $configuration->enableFixing();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpectedFileInfos = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $changedContent = $this->snippetFormatter->format(
            $inputAndExpectedFileInfos->getInputFileInfo(),
            SnippetPattern::HERENOWDOC_SNIPPET_REGEX,
            'herenowdoc'
        );

        $expectedFileContent = $inputAndExpectedFileInfos->getExpectedFileContent();
        $this->assertSame($expectedFileContent, $changedContent, $fixtureFileInfo->getRelativeFilePathFromCwd());
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/FixturePhp73', '*.php.inc');
    }
}
