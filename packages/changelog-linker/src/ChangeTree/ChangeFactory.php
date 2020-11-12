<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\ChangeTree;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\ChangeTree\Resolver\ValueObject\CategoryResolver;
use Symplify\ChangelogLinker\ChangeTree\Resolver\ValueObject\PackageResolver;
use Symplify\ChangelogLinker\Git\ValueObject\GitCommitDateTagResolver;
use Symplify\ChangelogLinker\ValueObject\ChangeTree\Change;
use Symplify\ChangelogLinker\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ValueObject\ParameterProvider;

/**
 * @see \Symplify\ChangelogLinker\Tests\ChangeTree\ChangeFactory\ChangeFactoryTest
 */
final class ChangeFactory
{
    /**
     * @var string
     * @see https://regex101.com/r/QPRx0q/1
     */
    private const ASTERISK_REGEX = '#(\*)#';

    /**
     * @var string[]
     */
    private $authorsToIgnore = [];

    /**
     * @var \Symplify\ChangelogLinker\Git\ValueObject\GitCommitDateTagResolver
     */
    private $gitCommitDateTagResolver;

    /**
     * @var \Symplify\ChangelogLinker\ChangeTree\Resolver\ValueObject\CategoryResolver
     */
    private $categoryResolver;

    /**
     * @var \Symplify\ChangelogLinker\ChangeTree\Resolver\ValueObject\PackageResolver
     */
    private $packageResolver;

    public function __construct(
        GitCommitDateTagResolver $gitCommitDateTagResolver,
        CategoryResolver $categoryResolver,
        PackageResolver $packageResolver,
        ParameterProvider $parameterProvider
    ) {
        $this->gitCommitDateTagResolver = $gitCommitDateTagResolver;
        $this->categoryResolver = $categoryResolver;
        $this->authorsToIgnore = $parameterProvider->provideArrayParameter(Option::AUTHORS_TO_IGNORE);
        $this->packageResolver = $packageResolver;
    }

    /**
     * @param mixed[] $pullRequest
     */
    public function createFromPullRequest(array $pullRequest): Change
    {
        $message = sprintf('- [#%s] %s', $pullRequest['number'], $this->escapeMarkdown($pullRequest['title']));

        $author = $pullRequest['user']['login'] ?? '';

        // skip the main maintainer to prevent self-thanking floods
        if ($author && ! in_array($author, $this->authorsToIgnore, true)) {
            $message .= ', Thanks to @' . $author;
        }

        $category = $this->categoryResolver->resolveCategory($pullRequest['title']);
        $package = $this->packageResolver->resolvePackage($pullRequest['title']);
        $messageWithoutPackage = $this->resolveMessageWithoutPackage($message, $package);

        // @todo 'merge_commit_sha' || 'head'
        $pullRequestTag = $this->gitCommitDateTagResolver->resolveCommitToTag($pullRequest['merge_commit_sha']);

        return new Change($message, $category, $package, $messageWithoutPackage, $pullRequestTag);
    }

    private function escapeMarkdown(string $content): string
    {
        $content = trim($content);

        return Strings::replace($content, self::ASTERISK_REGEX, '\\\$1');
    }

    private function resolveMessageWithoutPackage(string $message, ?string $package): string
    {
        if ($package === null) {
            return $message;
        }

        // can be aliased (not the $package variable), so we need to check any naming
        return Strings::replace($message, PackageResolver::PACKAGE_NAME_REGEX, '');
    }
}
