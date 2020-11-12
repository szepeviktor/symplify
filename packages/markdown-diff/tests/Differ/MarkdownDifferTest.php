<?php

declare(strict_types=1);

namespace Symplify\MarkdownDiff\Tests\Differ;

use Symplify\MarkdownDiff\Differ\ValueObject\MarkdownDiffer;
use Symplify\MarkdownDiff\Tests\HttpKernel\ValueObject\MarkdownDiffKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class MarkdownDifferTest extends AbstractKernelTestCase
{
    /**
     * @var \Symplify\MarkdownDiff\Differ\ValueObject\MarkdownDiffer
     */
    private $markdownDiffer;

    protected function setUp(): void
    {
        $this->bootKernel(MarkdownDiffKernel::class);

        $this->markdownDiffer = self::$container->get(MarkdownDiffer::class);
    }

    public function test(): void
    {
        $currentDiff = $this->markdownDiffer->diff('old code', 'new code');
        $this->assertStringEqualsFile(__DIR__ . '/Fixture/expected_diff.txt', $currentDiff);
    }
}
