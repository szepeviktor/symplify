<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger\ValueObject;

use Symplify\MonorepoBuilder\Merge\ComposerKeyMerger\AbstractComposerKeyMerger;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;
final class PreferStableKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        if ($newComposerJson->getPreferStable() === null) {
            return;
        }
        $mainComposerJson->setPreferStable($newComposerJson->getPreferStable());
    }
}
