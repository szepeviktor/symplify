<?php

declare(strict_types=1);
namespace Symplify\ChangelogLinker\Configuration\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const IN_CATEGORIES = 'in-categories';
    /**
     * @var string
     */
    public const IN_PACKAGES = 'in-packages';
    /**
     * @var string
     */
    public const DRY_RUN = 'dry-run';
    /**
     * @var string
     */
    public const FILE = 'file';
    /**
     * @var string
     */
    public const SINCE_ID = 'since-id';
    /**
     * @var string
     */
    public const BASE_BRANCH = 'base-branch';
}
