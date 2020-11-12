<?php

declare(strict_types=1);
namespace Symplify\MonorepoBuilder\Merge\ComposerKeyMerger\ValueObject;

use Symplify\MonorepoBuilder\Merge\ComposerKeyMerger\AbstractComposerKeyMerger;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Contract\ComposerKeyMergerInterface;
use Symplify\MonorepoBuilder\Merge\ValueObject\AutoloadPathValidator;
final class AutoloadComposerKeyMerger extends AbstractComposerKeyMerger implements ComposerKeyMergerInterface
{
    /**
     * @var \Symplify\MonorepoBuilder\Merge\ValueObject\AutoloadPathValidator
     */
    private $autoloadPathValidator;
    public function __construct(AutoloadPathValidator $autoloadPathValidator)
    {
        $this->autoloadPathValidator = $autoloadPathValidator;
    }
    public function merge(ComposerJson $mainComposerJson, ComposerJson $newComposerJson): void
    {
        if ($newComposerJson->getAutoload() === []) {
            return;
        }
        $this->autoloadPathValidator->ensureAutoloadPathExists($newComposerJson);
        $autoload = $this->mergeRecursiveAndSort($mainComposerJson->getAutoload(), $newComposerJson->getAutoload());
        $mainComposerJson->setAutoload($autoload);
    }
}
