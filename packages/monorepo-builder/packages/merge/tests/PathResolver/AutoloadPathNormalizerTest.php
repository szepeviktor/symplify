<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\PathResolver;

use Symplify\MonorepoBuilder\Merge\PathResolver\ValueObject\AutoloadPathNormalizer;
use Symplify\MonorepoBuilder\Merge\Tests\ComposerJsonDecorator\AbstractComposerJsonDecoratorTest;
use Symplify\SmartFileSystem\ValueObject\SmartFileInfo;

final class AutoloadPathNormalizerTest extends AbstractComposerJsonDecoratorTest
{
    /**
     * @var \Symplify\MonorepoBuilder\Merge\PathResolver\ValueObject\AutoloadPathNormalizer
     */
    private $autoloadPathNormalizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->autoloadPathNormalizer = self::$container->get(AutoloadPathNormalizer::class);
    }

    public function test(): void
    {
        if (! defined('SYMPLIFY_MONOREPO')) {
            $this->markTestSkipped('Already tested on monorepo');
        }

        $autoloadFileInfo = new SmartFileInfo(__DIR__ . '/AutoloadPathNormalizerSource/autoload.json');
        $composerJson = $this->createComposerJson($autoloadFileInfo);

        $this->autoloadPathNormalizer->normalizeAutoloadPaths($composerJson, $autoloadFileInfo);
        $this->assertComposerJsonEquals(
            __DIR__ . '/AutoloadPathNormalizerSource/expected-autoload.json',
            $composerJson
        );
    }
}
