<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Application;

use Symplify\ChangelogLinker\ValueObject\ChangelogDumper;
use Symplify\ChangelogLinker\ChangelogLinker;
use Symplify\ChangelogLinker\ChangeTree\ValueObject\ChangeResolver;

final class ChangelogLinkerApplication
{
    /**
     * @var \Symplify\ChangelogLinker\ChangeTree\ValueObject\ChangeResolver
     */
    private $changeResolver;

    /**
     * @var \Symplify\ChangelogLinker\ValueObject\ChangelogDumper
     */
    private $changelogDumper;

    /**
     * @var ChangelogLinker
     */
    private $changelogLinker;

    public function __construct(
        ChangeResolver $changeResolver,
        ChangelogDumper $changelogDumper,
        ChangelogLinker $changelogLinker
    ) {
        $this->changeResolver = $changeResolver;
        $this->changelogDumper = $changelogDumper;
        $this->changelogLinker = $changelogLinker;
    }

    public function createContentFromPullRequestsBySortPriority(
        array $pullRequests,
        ?string $sortPriority,
        bool $inCategories,
        bool $inPackages
    ): string {
        $changes = $this->changeResolver->resolveSortedChangesFromPullRequestsWithSortPriority(
            $pullRequests,
            $sortPriority
        );

        $content = $this->changelogDumper->reportChangesWithHeadlines(
            $changes,
            $inCategories,
            $inPackages,
            $sortPriority
        );

        return $this->changelogLinker->processContent($content);
    }
}
