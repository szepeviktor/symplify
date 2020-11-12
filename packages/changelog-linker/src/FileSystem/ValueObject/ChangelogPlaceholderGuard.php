<?php

declare(strict_types=1);
namespace Symplify\ChangelogLinker\FileSystem\ValueObject;

use Nette\Utils\Strings;
use Symplify\ChangelogLinker\ValueObject\MissingPlaceholderInChangelogException;
final class ChangelogPlaceholderGuard
{
    public function ensurePlaceholderIsPresent(string $changelogContent, string $placeholder): void
    {
        if (Strings::contains($changelogContent, $placeholder)) {
            return;
        }
        throw new MissingPlaceholderInChangelogException(sprintf('There is missing "%s" placeholder in CHANGELOG.md. Put it where you want to add dumped merges.', $placeholder));
    }
}
