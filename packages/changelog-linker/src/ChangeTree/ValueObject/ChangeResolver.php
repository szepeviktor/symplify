<?php

declare(strict_types=1);
namespace Symplify\ChangelogLinker\ChangeTree\ValueObject;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\ValueObject\ChangeTree\Change;
use Symplify\ChangelogLinker\ValueObject\RegexPattern;
final class ChangeResolver
{
    /**
     * @var ChangeFactory
     */
    private $changeFactory;
    /**
     * @var \Symplify\ChangelogLinker\ChangeTree\ValueObject\ChangeSorter
     */
    private $changeSorter;
    public function __construct(\Symplify\ChangelogLinker\ChangeTree\ChangeFactory $changeFactory, ChangeSorter $changeSorter)
    {
        $this->changeFactory = $changeFactory;
        $this->changeSorter = $changeSorter;
    }
    /**
     * @param mixed[] $pullRequests
     * @return Change[]
     */
    public function resolveSortedChangesFromPullRequestsWithSortPriority(array $pullRequests, ?string $sortPriority): array
    {
        $changes = [];
        foreach ($pullRequests as $pullRequest) {
            $changes[] = $this->changeFactory->createFromPullRequest($pullRequest);
        }
        $changes = $this->filterOutUselessChanges($changes);
        return $this->changeSorter->sort($changes, $sortPriority);
    }
    /**
     * @param Change[] $changes
     * @return Change[]
     */
    private function filterOutUselessChanges(array $changes): array
    {
        return array_filter($changes, function (Change $change): bool {
            // skip new/fixed tests
            return !Strings::match($change->getMessage(), RegexPattern::TEST_TITLE_REGEX);
        });
    }
}
