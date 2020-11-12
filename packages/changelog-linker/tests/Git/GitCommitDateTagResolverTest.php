<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\Git;

use Iterator;
use Symplify\ChangelogLinker\Git\ValueObject\GitCommitDateTagResolver;
use Symplify\ChangelogLinker\HttpKernel\ValueObject\ChangelogLinkerKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class GitCommitDateTagResolverTest extends AbstractKernelTestCase
{
    /**
     * @var \Symplify\ChangelogLinker\Git\ValueObject\GitCommitDateTagResolver
     */
    private $gitCommitDateTagResolver;

    protected function setUp(): void
    {
        $this->bootKernel(ChangelogLinkerKernel::class);
        $this->gitCommitDateTagResolver = self::$container->get(GitCommitDateTagResolver::class);

        $this->markTestSkipped('Random false positives on Github Actions');
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $commitHash, string $expectedTag): void
    {
        $this->assertSame($expectedTag, $this->gitCommitDateTagResolver->resolveCommitToTag($commitHash));
    }

    public function provideData(): Iterator
    {
        // different commit hashes after monorepo
        yield ['ef5e708', 'v4.1.1'];
        yield ['940ec99', 'v3.2.26'];
        yield ['too-new', 'Unreleased'];
    }

    /**
     * @dataProvider provideDataResolveDateForTag()
     */
    public function testResolveDateForTag(string $tag, ?string $expectedTag): void
    {
        $this->assertSame($expectedTag, $this->gitCommitDateTagResolver->resolveDateForTag($tag));
    }

    public function provideDataResolveDateForTag(): Iterator
    {
        yield ['v4.4.1', '2018-06-07'];
        yield ['v4.4.2', '2018-06-10'];
        yield ['Unreleased', null];
    }
}
