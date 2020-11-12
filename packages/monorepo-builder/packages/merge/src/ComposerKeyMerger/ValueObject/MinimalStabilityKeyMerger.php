<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger\ValueObject;

use Symplify\MonorepoBuilder\Merge\ComposerKeyMerger\AbstractComposerKeyMerger;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;
final class MinimalStabilityKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        if ($newComposerJson->getMinimumStability() === null) {
            return;
        }
        $mainComposerJson->setMinimumStability($newComposerJson->getMinimumStability());
    }
}
